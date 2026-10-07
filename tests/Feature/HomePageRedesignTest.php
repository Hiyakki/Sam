<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SAM Orthotic Prosthetic');
        $response->assertSee('Buat Janji Kunjungan');
        $response->assertSee('Chat WhatsApp');
    }

    public function test_root_category_aggregates_child_products_count_correctly(): void
    {
        $parentCategory = ProductCategory::create([
            'name' => 'Orthosis Test',
            'slug' => 'orthosis-test',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $childCategory = ProductCategory::create([
            'name' => 'Knee Orthosis',
            'slug' => 'knee-orthosis',
            'parent_id' => $parentCategory->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'category_id' => $childCategory->id,
            'name' => 'Functional Knee Brace',
            'slug' => 'functional-knee-brace',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $childCategory->id,
            'name' => 'Osteoarthritis Unloader Brace',
            'slug' => 'oa-unloader-brace',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Orthosis Test');
        // Kategori induk harus menampilkan total 2 produk dari anak
        $response->assertSee('2 Produk');
    }

    public function test_testimonials_without_consent_are_hidden(): void
    {
        // Testimoni dengan persetujuan (harus tampil)
        Testimonial::create([
            'name' => 'Bapak Hartono',
            'role_or_info' => 'Pengguna Prosthesis',
            'content' => 'Pelayanan sangat memuaskan dan presisi.',
            'rating' => 5,
            'is_active' => true,
            'consent_confirmed' => true,
            'sort_order' => 1,
        ]);

        // Testimoni tanpa persetujuan (tidak aktif)
        Testimonial::create([
            'name' => 'Ibu Rahmawati Tanpa Izin',
            'role_or_info' => 'Pasien Privat',
            'content' => 'Komentar rahasia pasien tanpa consent.',
            'rating' => 5,
            'is_active' => false,
            'consent_confirmed' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bapak Hartono');
        $response->assertSee('Pelayanan sangat memuaskan dan presisi.');
        $response->assertDontSee('Ibu Rahmawati Tanpa Izin');
        $response->assertDontSee('Komentar rahasia pasien tanpa consent.');
    }

    public function test_empty_testimonial_section_is_hidden(): void
    {
        // Pastikan tidak ada testimoni dengan consent
        Testimonial::query()->delete();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Cerita & Pengalaman Pasien');
    }

    public function test_faq_section_is_hidden_when_no_home_faqs(): void
    {
        // Pastikan tidak ada FAQ show_on_home
        Faq::query()->delete();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Pertanyaan yang Sering Diajukan');
    }

    public function test_branches_section_displays_active_branches(): void
    {
        Branch::create([
            'name' => 'Cabang Surakarta',
            'slug' => 'cabang-surakarta',
            'address' => 'Jl. Kebangkitan No. 10, Solo',
            'phone_wa' => '081234567899',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Cabang Surakarta');
        $response->assertSee('Jl. Kebangkitan No. 10, Solo');
    }
}
