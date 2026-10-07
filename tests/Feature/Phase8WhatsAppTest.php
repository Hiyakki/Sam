<?php

namespace Tests\Feature;

use App\Filament\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Service;
use App\Services\SlotService;
use App\Services\WhatsApp\FonnteSender;
use App\Services\WhatsApp\NullSender;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Phase8WhatsAppTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Pemeriksaan Ortotik',
            'slug' => 'pemeriksaan-ortotik',
            'is_active' => true,
        ]);

        $this->branch->services()->attach($this->service->id);

        for ($day = 1; $day <= 7; $day++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $day,
                'is_open' => true,
                'open_time' => '08:00:00',
                'close_time' => '12:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 2,
            ]);
        }
    }

    public function test_null_sender_returns_true_without_http_calls(): void
    {
        Http::preventStrayRequests();

        $sender = new NullSender;
        $result = $sender->send('081234567890', 'Uji coba pesan');

        $this->assertTrue($result);
        Http::assertNothingSent();
    }

    public function test_fonnte_sender_sends_http_post_with_token_and_payload(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $sender = new FonnteSender('test-secret-token');
        $result = $sender->send('081234567890', 'Halo dari SAM');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'test-secret-token')
                && $request['target'] === '6281234567890'
                && $request['message'] === 'Halo dari SAM';
        });
    }

    public function test_fonnte_sender_is_fail_safe_on_server_error_or_timeout(): void
    {
        Http::fake([
            'https://api.fonnte.com/send' => Http::response('Internal Server Error', 500),
        ]);

        $sender = new FonnteSender('test-secret-token');

        // Harus return false tanpa melempar exception ke luar
        $result = $sender->send('081234567890', 'Pesan saat server error');
        $this->assertFalse($result);
    }

    public function test_appointment_booking_succeeds_even_if_whatsapp_gateway_fails(): void
    {
        Config::set('services.whatsapp.enabled', true);
        Config::set('services.whatsapp.driver', 'fonnte');
        Config::set('services.whatsapp.token', 'dummy-token');

        // Simulasi gateway Fonnte error 503
        Http::fake([
            '*' => Http::response('Service Unavailable', 503),
        ]);

        $targetDate = Carbon::now('Asia/Jakarta')->addDays(3)->format('Y-m-d');
        $slotService = app(SlotService::class);

        // Booking tidak boleh gagal karena kegagalan gateway WhatsApp
        $appointment = $slotService->book([
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '09:00',
            'patient_name' => 'Pasien Tangguh',
            'patient_email' => 'tangguh@example.com',
            'patient_phone' => '081234567899',
            'patient_age' => 30,
            'complaint' => 'Keluhan',
        ]);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'patient_name' => 'Pasien Tangguh',
        ]);
    }

    public function test_filament_appointment_resource_generates_valid_whatsapp_url(): void
    {
        $appointment = Appointment::create([
            'code' => 'SAMWA001',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => '2026-10-30',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'patient_name' => 'Dewi Sartika',
            'patient_email' => 'dewi@example.com',
            'patient_phone' => '628123456789',
            'patient_age' => 25,
            'complaint' => 'Konsultasi ortotik',
            'status' => 'confirmed',
        ]);

        $url = AppointmentResource::whatsAppUrl($appointment);

        $this->assertStringStartsWith('https://wa.me/628123456789?text=', $url);
        $this->assertStringContainsString('Dewi%20Sartika', $url);
        $this->assertStringContainsString('SAMWA001', $url);
    }
}
