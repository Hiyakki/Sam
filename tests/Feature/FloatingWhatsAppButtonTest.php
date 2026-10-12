<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FloatingWhatsAppButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_floating_whatsapp_button_appears_on_public_pages(): void
    {
        SiteSetting::set('contact.whatsapp', '081234567890');

        $pages = [
            route('home'),
            route('about'),
            route('catalog.index'),
            route('faq'),
            route('testimonials.index'),
            route('appointment'),
            route('privacy'),
        ];

        foreach ($pages as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertSee('Kontak Cepat WhatsApp', false);
            $response->assertSee('https://wa.me/6281234567890', false);
        }
    }
}
