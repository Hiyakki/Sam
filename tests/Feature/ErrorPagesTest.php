<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSetting::set('contact.whatsapp', '081234567890');
    }

    public function test_404_error_page_renders_with_brand_styling_and_links(): void
    {
        $response = $this->get('/halaman-yang-pasti-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('Hubungi WhatsApp');
    }

    public function test_419_page_renders_correctly(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('419');
        $view->assertSee('Sesi Kedaluwarsa');
        $view->assertSee('Kembali ke Beranda');
        $view->assertSee('Hubungi WhatsApp');
    }

    public function test_429_page_renders_correctly(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('429');
        $view->assertSee('Terlalu Banyak Permintaan');
        $view->assertSee('Kembali ke Beranda');
    }

    public function test_500_page_renders_correctly(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Terjadi Kendala Internal Server');
        $view->assertSee('Kembali ke Beranda');
    }
}
