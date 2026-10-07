<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSetting::set('company.name', 'SAM Orthotic Prosthetic');
        SiteSetting::set('company.whatsapp', '081234567890');
        SiteSetting::set('general.privacy_policy', '<h2>Kebijakan Privasi Resmi</h2><p>Data pasien dijamin aman dan rahasia.</p>');
    }

    public function test_home_page_returns_ok_with_dynamic_content(): void
    {
        $parent = ProductCategory::create([
            'name' => 'Orthosis',
            'slug' => 'orthosis',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $parent->id,
            'name' => 'Ankle Foot Orthosis Kustom',
            'slug' => 'afo-kustom',
            'function' => 'Menstabilkan sendi pergelangan kaki',
            'indication' => 'Drop foot atau post stroke',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Active and consented testimonial
        Testimonial::create([
            'name' => 'Bapak Sudirman',
            'content' => 'Sangat terbantu dengan kaki palsu dari SAM.',
            'product_id' => $product->id,
            'rating' => 5,
            'is_active' => true,
            'consent_confirmed' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('SAM Orthotic Prosthetic');
        $response->assertSee('Ankle Foot Orthosis Kustom');
        $response->assertSee('Bapak Sudirman');
    }

    public function test_about_page_returns_ok_and_lists_active_branches(): void
    {
        Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-solo',
            'address' => 'Jl. Veteran No. 12, Surakarta',
            'phone_wa' => '081234567891',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Cabang Nonaktif',
            'slug' => 'cabang-nonaktif',
            'address' => 'Alamat cabang tutup',
            'phone_wa' => '081234567899',
            'is_active' => false,
        ]);

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSee('SAM Cabang Solo');
        $response->assertDontSee('Cabang Nonaktif');
    }

    public function test_catalog_index_displays_active_products_without_price(): void
    {
        $category = ProductCategory::create([
            'name' => 'Prosthesis',
            'slug' => 'prosthesis',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Kaki Palsu Bawah Lutut',
            'slug' => 'kaki-palsu-bawah-lutut',
            'function' => 'Amputasi transtibial',
            'price' => 5000000,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Rahasia Nonaktif',
            'slug' => 'produk-nonaktif',
            'price' => 10000000,
            'is_active' => false,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Kaki Palsu Bawah Lutut');
        $response->assertDontSee('Produk Rahasia Nonaktif');
        // Pastikan nominal harga tidak dipajang secara gamblang di katalog publik
        $response->assertDontSee('Rp 5.000.000');
        $response->assertDontSee('Rp5.000.000');
        $response->assertDontSee('5000000');
    }

    public function test_catalog_search_filters_products_correctly(): void
    {
        $category = ProductCategory::create([
            'name' => 'Orthosis',
            'slug' => 'orthosis',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Boston Scoliosis Brace',
            'slug' => 'boston-brace',
            'indication' => 'Skoliosis idiopatik remaja',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Knee Ankle Foot Orthosis',
            'slug' => 'kafo-kustom',
            'indication' => 'Kelemahan quadriceps',
            'is_active' => true,
        ]);

        // Search for 'Boston'
        $response = $this->get(route('catalog.index', ['search' => 'Boston']));
        $response->assertStatus(200);
        $response->assertSee('Boston Scoliosis Brace');
        $response->assertDontSee('Knee Ankle Foot Orthosis');

        // Search for 'quadriceps' in indication
        $response2 = $this->get(route('catalog.index', ['q' => 'quadriceps']));
        $response2->assertStatus(200);
        $response2->assertSee('Knee Ankle Foot Orthosis');
        $response2->assertDontSee('Boston Scoliosis Brace');
    }

    public function test_catalog_category_filter_works(): void
    {
        $cat1 = ProductCategory::create(['name' => 'Kategori A', 'slug' => 'kat-a', 'is_active' => true]);
        $cat2 = ProductCategory::create(['name' => 'Kategori B', 'slug' => 'kat-b', 'is_active' => true]);

        Product::create(['category_id' => $cat1->id, 'name' => 'Barang A', 'slug' => 'barang-a', 'is_active' => true]);
        Product::create(['category_id' => $cat2->id, 'name' => 'Barang B', 'slug' => 'barang-b', 'is_active' => true]);

        $response = $this->get(route('catalog.index', ['kategori' => 'kat-a']));
        $response->assertStatus(200);
        $response->assertSee('Barang A');
        $response->assertDontSee('Barang B');
    }

    public function test_catalog_show_displays_active_product_detail(): void
    {
        $category = ProductCategory::create(['name' => 'Komponen', 'slug' => 'komponen', 'is_active' => true]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sendi Lutut Hidrolik',
            'slug' => 'sendi-lutut-hidrolik',
            'function' => 'Kontrol fase ayunan stabil',
            'indication' => 'Tingkat aktivitas K3 ke atas',
            'description' => '<p>Komponen sendi lutut prostetik performa tinggi.</p>',
            'price' => 15000000,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', 'sendi-lutut-hidrolik'));

        $response->assertStatus(200);
        $response->assertSee('Sendi Lutut Hidrolik');
        $response->assertSee('Kontrol fase ayunan stabil');
        $response->assertSee('Tanya Spesifikasi via WhatsApp');
        $response->assertDontSee('Rp 15.000.000');
    }

    public function test_catalog_show_returns_404_for_inactive_or_missing_product(): void
    {
        $category = ProductCategory::create(['name' => 'Komponen', 'slug' => 'komponen', 'is_active' => true]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Nonaktif',
            'slug' => 'produk-nonaktif',
            'is_active' => false,
        ]);

        $this->get(route('catalog.show', 'produk-nonaktif'))->assertStatus(404);
        $this->get(route('catalog.show', 'produk-tidak-pernah-ada'))->assertStatus(404);
    }

    public function test_faq_page_displays_active_faqs_grouped_by_category(): void
    {
        Faq::create([
            'category' => 'Konsultasi & Janji Temu',
            'question' => 'Apakah harus membawa rujukan dokter?',
            'answer' => 'Rujukan dokter sangat membantu tim klinis kami.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Faq::create([
            'category' => 'Garansi',
            'question' => 'Pertanyaan rahasia nonaktif?',
            'answer' => 'Jawaban rahasia.',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('Konsultasi & Janji Temu');
        $response->assertSee('Apakah harus membawa rujukan dokter?');
        $response->assertDontSee('Pertanyaan rahasia nonaktif?');
    }

    public function test_privacy_policy_page_displays_content_from_settings(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi Resmi');
        $response->assertSee('Data pasien dijamin aman dan rahasia.');
    }

    public function test_sitemap_xml_returns_valid_xml_with_public_urls(): void
    {
        $category = ProductCategory::create(['name' => 'Orthosis', 'slug' => 'orthosis', 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'KAFO Pediatrik',
            'slug' => 'kafo-pediatrik',
            'is_active' => true,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee(route('home'));
        $response->assertSee(route('catalog.show', 'kafo-pediatrik'));
    }

    public function test_robots_txt_returns_text_disallowing_admin_panel(): void
    {
        $response = $this->get(route('robots'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', (string) $response->headers->get('Content-Type'));
        $response->assertSee('Disallow: /kelola-sam/');
        $response->assertSee(route('sitemap'));
    }

    public function test_inactive_testimonials_are_hidden_from_home_and_product_pages(): void
    {
        $category = ProductCategory::create(['name' => 'Orthosis', 'slug' => 'orthosis', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Korset Lumbal',
            'slug' => 'korset-lumbal',
            'is_active' => true,
        ]);

        Testimonial::create([
            'name' => 'Ibu Rahasia',
            'content' => 'Testimoni yang belum boleh tayang.',
            'product_id' => $product->id,
            'is_active' => false,
            'consent_confirmed' => false,
        ]);

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertDontSee('Ibu Rahasia');
        $homeResponse->assertDontSee('Testimoni yang belum boleh tayang.');

        $productResponse = $this->get(route('catalog.show', 'korset-lumbal'));
        $productResponse->assertDontSee('Ibu Rahasia');
        $productResponse->assertDontSee('Testimoni yang belum boleh tayang.');
    }
}
