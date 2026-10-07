<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_returns_successful_response(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Tentang SAM');
        $response->assertSee('Perjalanan Dedikasi Sejak 2014');
    }

    public function test_about_page_displays_accurate_factual_stats(): void
    {
        // Setup kategori dan produk
        $cat = ProductCategory::create([
            'name' => 'Kategori Uji',
            'slug' => 'kategori-uji',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Produk A',
            'slug' => 'produk-a',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $cat->id,
            'name' => 'Produk B',
            'slug' => 'produk-b',
            'is_active' => true,
        ]);

        // Setup cabang
        Branch::create([
            'name' => 'Cabang Pusat',
            'slug' => 'cabang-pusat',
            'address' => 'Solo',
            'phone_wa' => '081234567890',
            'is_active' => true,
        ]);

        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('2014'); // Tahun berdiri
        $response->assertSee('Produk & Komponen Katalog');
        $response->assertSee('Kantor Cabang Aktif');
    }

    public function test_vision_and_mission_are_displayed(): void
    {
        SiteSetting::set('about.vision', 'Menjadi pusat ortotik prostetik terpercaya dan humanis.');
        SiteSetting::set('about.mission', "1. Memberikan layanan terbaik.\n2. Presisi tinggi.");

        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Menjadi pusat ortotik prostetik terpercaya dan humanis.');
        $response->assertSee('Memberikan layanan terbaik.');
    }

    public function test_team_section_hidden_when_empty(): void
    {
        SiteSetting::set('about.team', []);

        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertDontSee('Tim Ahli & Praktisi');
    }
}
