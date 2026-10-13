<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase9SecurityAndSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_responses_contain_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_home_page_contains_schema_org_json_ld_structured_data(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "MedicalBusiness"', false);
        $response->assertSee('"Orthotics"', false);
        $response->assertSee('"Prosthetics"', false);
    }

    public function test_default_admin_and_register_routes_return_404(): void
    {
        // URL default /admin wajib 404 (harus /kelola-sam)
        $this->get('/admin')->assertStatus(404);

        // Halaman registrasi publik wajib 404 (hanya 1 akun admin lewat seeder)
        $this->get('/register')->assertStatus(404);

        // URL login admin sah di /kelola-sam/login
        $this->get('/kelola-sam/login')->assertStatus(200);
    }

    public function test_private_prescription_download_requires_admin_authentication(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('prescriptions/secret-resep.pdf', 'Dokumen medis privat');

        $branch = Branch::create([
            'name' => 'SAM Solo',
            'slug' => 'sam-solo',
            'address' => 'Solo',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $service = Service::create([
            'name' => 'Layanan',
            'slug' => 'layanan',
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'appointment_date' => '2026-10-25',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Pasien Privat',
            'patient_email' => 'privat@example.com',
            'patient_phone' => '628111111111',
            'patient_age' => 45,
            'complaint' => 'Privat',
            'prescription_path' => 'prescriptions/secret-resep.pdf',
            'status' => 'pending',
        ]);

        $route = route('filament.kelola-sam.prescription.download', $appointment);

        // 1. Tanpa autentikasi admin: ditolak (dialihkan ke login /kelola-sam/login)
        $unauthenticated = $this->get($route);
        $unauthenticated->assertRedirect(route('filament.kelola-sam.auth.login'));

        // 2. Dengan autentikasi admin: berhasil mengunduh file
        $admin = User::factory()->create();
        $authenticated = $this->actingAs($admin)->get($route);
        $authenticated->assertStatus(200);
        $authenticated->assertHeader('Content-Disposition', "attachment; filename=resep-janji-{$appointment->id}.pdf");
    }

    public function test_open_graph_and_meta_tags_exist_on_public_pages(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        $response->assertSee('<meta property="og:url"', false);
    }
}
