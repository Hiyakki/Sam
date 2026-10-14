<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Phase3AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_admin_url_returns_404(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(404);
    }

    public function test_registration_route_is_disabled(): void
    {
        $response = $this->get('/kelola-sam/register');

        $response->assertStatus(404);
    }

    public function test_password_reset_routes_are_disabled(): void
    {
        $this->get('/kelola-sam/password-reset/request')->assertStatus(404);
        $this->get('/kelola-sam/password-reset/reset')->assertStatus(404);
    }

    public function test_login_page_does_not_contain_forgot_password_link(): void
    {
        $response = $this->get('/kelola-sam/login');

        $response->assertOk();
        $response->assertDontSee('Lupa kata sandi');
        $response->assertDontSee('password-reset');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/kelola-sam');

        $response->assertRedirect('/kelola-sam/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/kelola-sam');

        $response->assertOk();
    }

    public function test_admin_can_access_branches(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/branches')->assertOk();
    }

    public function test_admin_can_access_services(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/services')->assertOk();
    }

    public function test_admin_can_access_branch_closures(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/branch-closures')->assertOk();
    }

    public function test_admin_can_access_appointments(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/appointments')->assertOk();
    }

    public function test_admin_can_access_product_categories(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/product-categories')->assertOk();
    }

    public function test_admin_can_access_products(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/products')->assertOk();
    }

    public function test_admin_can_access_faqs(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/faqs')->assertOk();
    }

    public function test_admin_can_access_testimonials(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/testimonials')->assertOk();
    }

    public function test_admin_can_access_site_settings(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/kelola-sam/pengaturan-situs')->assertOk();
    }

    public function test_testimonial_cannot_be_active_without_consent(): void
    {
        $this->expectException(ValidationException::class);

        Testimonial::create([
            'name' => 'Pasien A',
            'content' => 'Pelayanan sangat memuaskan.',
            'is_active' => true,
            'consent_confirmed' => false,
        ]);
    }

    public function test_testimonial_can_be_active_with_consent(): void
    {
        $testimonial = Testimonial::create([
            'name' => 'Pasien A',
            'content' => 'Pelayanan sangat memuaskan.',
            'is_active' => true,
            'consent_confirmed' => true,
        ]);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'is_active' => 1,
            'consent_confirmed' => 1,
        ]);
    }

    public function test_prescription_download_requires_authentication(): void
    {
        $branch = Branch::create([
            'name' => 'SAM Jakarta',
            'slug' => 'sam-jakarta',
            'address' => 'Jl. Percobaan No. 1',
            'phone_wa' => '628123456789',
        ]);

        $service = Service::create([
            'name' => 'Konsultasi',
            'slug' => 'konsultasi',
            'sort_order' => 1,
        ]);

        $appointment = Appointment::create([
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'budi@example.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 30,
            'complaint' => 'Kaki nyeri saat berjalan',
            'prescription_path' => 'prescriptions/test.pdf',
            'status' => 'pending',
        ]);

        $response = $this->get(route('filament.kelola-sam.prescription.download', $appointment));
        $response->assertRedirect('/kelola-sam/login');
    }

    public function test_authenticated_admin_can_download_existing_prescription(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('prescriptions/test.pdf', 'dummy pdf content');

        $admin = User::factory()->create();

        $branch = Branch::create([
            'name' => 'SAM Jakarta',
            'slug' => 'sam-jakarta',
            'address' => 'Jl. Percobaan No. 1',
            'phone_wa' => '628123456789',
        ]);

        $service = Service::create([
            'name' => 'Konsultasi',
            'slug' => 'konsultasi',
            'sort_order' => 1,
        ]);

        $appointment = Appointment::create([
            'branch_id' => $branch->id,
            'service_id' => $service->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'patient_name' => 'Budi Santoso',
            'patient_email' => 'budi@example.com',
            'patient_phone' => '6281234567890',
            'patient_age' => 30,
            'complaint' => 'Kaki nyeri saat berjalan',
            'prescription_path' => 'prescriptions/test.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('filament.kelola-sam.prescription.download', $appointment));

        $response->assertOk();
        $response->assertHeader('content-disposition', "attachment; filename=resep-janji-{$appointment->id}.pdf");
    }
}
