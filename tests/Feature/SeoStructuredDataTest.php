<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Branch::create([
            'name' => 'Solo',
            'slug' => 'solo',
            'address' => 'Jl. Veteran No. 12',
            'city' => 'Surakarta',
            'phone_wa' => '081234567890',
            'is_active' => true,
            'latitude' => -7.5666,
            'longitude' => 110.8166,
        ]);
    }

    public function test_home_page_contains_canonical_and_local_business_schema(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('LocalBusiness', false);
        $response->assertSee('MedicalBusiness', false);
        $response->assertSee('Cabang Solo', false);
        $response->assertSee('og:title', false);
    }

    public function test_about_page_contains_canonical_and_local_business_schema(): void
    {
        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('LocalBusiness', false);
        $response->assertSee('Cabang Solo', false);
    }

    public function test_faq_page_contains_faq_page_schema(): void
    {
        Faq::create([
            'question' => 'Apakah butuh resep dokter?',
            'answer' => 'Resep dokter sangat dianjurkan untuk pembuatan ortotik prostetik.',
            'category' => 'Konsultasi & Reservasi',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('Apakah butuh resep dokter?', false);
        $response->assertSee('Resep dokter sangat dianjurkan', false);
    }

    public function test_catalog_detail_contains_product_schema_and_og(): void
    {
        $category = ProductCategory::create([
            'name' => 'Kaki Palsu',
            'slug' => 'kaki-palsu',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Kaki Palsu Bawah Lutut Transtibial',
            'slug' => 'kaki-palsu-bawah-lutut-transtibial',
            'category_id' => $category->id,
            'description' => 'Kaki palsu bawah lutut presisi kustom.',
            'function' => 'Membantu mobilitas amputasi bawah lutut.',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('Kaki Palsu Bawah Lutut Transtibial', false);
        $response->assertSee('SAM Orthotic Prosthetic', false);
    }
}
