<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AntiSpamAndRateLimitTest extends TestCase
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
            'name' => 'Konsultasi Ortotik Kustom',
            'slug' => 'konsultasi-ortotik',
            'is_active' => true,
        ]);

        $this->branch->services()->attach($this->service->id);

        for ($day = 1; $day <= 7; $day++) {
            BranchSchedule::create([
                'branch_id' => $this->branch->id,
                'day_of_week' => $day,
                'is_open' => ($day !== 7),
                'open_time' => '08:00:00',
                'close_time' => '17:00:00',
                'slot_duration_minutes' => 60,
                'capacity_per_slot' => 2,
            ]);
        }
    }

    public function test_rate_limit_blocks_excessive_submissions_per_ip(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');
        $payload = [
            'patient_name' => 'Pasien Normal',
            'patient_email' => 'pasien@example.com',
            'patient_phone' => '081234567890',
            'patient_age' => 30,
            'complaint' => 'Pemeriksaan rutin kaki',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => '',
        ];

        // 3 pengajuan pertama
        for ($i = 0; $i < 3; $i++) {
            $payload['start_time'] = sprintf('%02d:00', 8 + $i);
            $payload['patient_phone'] = '08123456789'.$i;
            $this->post(route('appointment.store'), $payload);
        }

        // Pengajuan ke-4 terblokir rate limit per IP
        $payload['start_time'] = '11:00';
        $payload['patient_phone'] = '081234567899';
        $response = $this->post(route('appointment.store'), $payload);
        $response->assertSessionHasErrors('rate_limit');
    }

    public function test_rate_limit_blocks_excessive_submissions_per_phone(): void
    {
        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');
        $payload = [
            'patient_name' => 'Pasien Normal',
            'patient_email' => 'pasien@example.com',
            'patient_phone' => '085555555555',
            'patient_age' => 30,
            'complaint' => 'Pemeriksaan rutin kaki',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => '',
        ];

        // Simulasikan request dari IP berbeda tapi nomor telepon sama
        for ($i = 0; $i < 3; $i++) {
            $payload['start_time'] = sprintf('%02d:00', 8 + $i);
            $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.'.($i + 1)])
                ->post(route('appointment.store'), $payload);
        }

        // Pengajuan ke-4 dengan IP baru tapi nomor telepon sama
        $payload['start_time'] = '11:00';
        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.99'])
            ->post(route('appointment.store'), $payload);

        $response->assertSessionHasErrors('patient_phone');
    }

    public function test_turnstile_auto_disabled_when_secret_blank(): void
    {
        config(['services.turnstile.secret' => '']);

        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');
        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Pasien Tanpa Captcha',
            'patient_email' => 'pasien@example.com',
            'patient_phone' => '087777777777',
            'patient_age' => 25,
            'complaint' => 'Keluhan',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => '',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_turnstile_fails_when_challenge_invalid(): void
    {
        config(['services.turnstile.secret' => 'dummy_secret_key']);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false], 200),
        ]);

        $targetDate = Carbon::now('Asia/Jakarta')->next(Carbon::MONDAY)->format('Y-m-d');
        $response = $this->post(route('appointment.store'), [
            'patient_name' => 'Pasien Fake Captcha',
            'patient_email' => 'pasien@example.com',
            'patient_phone' => '088888888888',
            'patient_age' => 25,
            'complaint' => 'Keluhan',
            'branch_id' => $this->branch->id,
            'service_id' => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time' => '08:00',
            'privacy_consent' => '1',
            'website_hp' => '',
            'cf-turnstile-response' => 'invalid_token',
        ]);

        $response->assertSessionHasErrors('cf-turnstile-response');
    }
}
