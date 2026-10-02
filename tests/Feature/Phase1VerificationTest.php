<?php

namespace Tests\Feature;

use Tests\TestCase;

class Phase1VerificationTest extends TestCase
{
    public function test_home_page_is_accessible_and_renders_layout(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SAM');
        $response->assertSee('Orthotic Prosthetic');
        $response->assertSee('Buat Janji');
    }

    public function test_about_page_is_accessible(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Tentang SAM Orthotic Prosthetic');
    }

    public function test_catalog_index_page_is_accessible(): void
    {
        $response = $this->get('/katalog');

        $response->assertStatus(200);
        $response->assertSee('Katalog');
    }

    public function test_catalog_show_page_is_accessible(): void
    {
        $response = $this->get('/katalog/afo-brace');

        $response->assertStatus(200);
        $response->assertSee('afo brace');
    }

    public function test_faq_page_is_accessible(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang Sering Diajukan');
    }

    public function test_appointment_page_is_accessible(): void
    {
        $response = $this->get('/appointment');

        $response->assertStatus(200);
        $response->assertSee('Buat Janji Temu');
    }

    public function test_privacy_page_is_accessible(): void
    {
        $response = $this->get('/kebijakan-privasi');

        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi');
    }

    public function test_admin_panel_is_at_kelola_sam_login(): void
    {
        $response = $this->get('/kelola-sam/login');

        $response->assertStatus(200);
        $response->assertSee('SAM Orthotic Prosthetic');
    }

    public function test_default_admin_url_returns_404(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(404);
    }

    public function test_registration_is_not_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(404);
    }
}
