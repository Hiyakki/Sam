<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Redirect;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SeoAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedDatabaseForSeo();
    }

    protected function seedDatabaseForSeo(): void
    {
        $category = ProductCategory::create([
            'name' => 'Kaki Palsu',
            'slug' => 'kaki-palsu',
            'intro_text' => 'Kategori alat bantu mobilitas kaki tiruan untuk mendukung aktivitas mandiri pasien amputasi tubuh bagian bawah.',
            'meta_title' => 'Kaki Palsu Kustom | Katalog | SAM',
            'meta_description' => 'Koleksi produk kaki palsu kustom bawah dan atas lutut presisi dari SAM Orthotic Prosthetic sejak 2014.',
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Kaki Palsu Bawah Lutut Transtibial',
            'slug' => 'kaki-palsu-bawah-lutut-transtibial',
            'function' => 'Membantu mobilitas amputasi bawah lutut',
            'indication' => 'Amputasi transtibial traumatik atau diabetes',
            'description' => 'Kaki palsu bawah lutut presisi kustom dengan soket nyaman.',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Kaki Palsu Nonaktif',
            'slug' => 'kaki-palsu-nonaktif',
            'function' => 'Hanya untuk pengujian',
            'is_active' => false,
        ]);

        $branch = Branch::create([
            'name' => 'SAM Cabang Solo',
            'slug' => 'sam-cabang-solo',
            'address' => 'Jl. Kebangkitan No. 10, Surakarta, Jawa Tengah',
            'phone_wa' => '081234567890',
            'email' => 'solo@sam.co.id',
            'is_active' => true,
        ]);

        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 1, // Senin
            'is_open' => true,
            'open_time' => '08:00',
            'close_time' => '17:00',
        ]);

        BranchSchedule::create([
            'branch_id' => $branch->id,
            'day_of_week' => 0, // Minggu (Tutup)
            'is_open' => false,
            'open_time' => null,
            'close_time' => null,
        ]);

        Faq::create([
            'question' => 'Apakah butuh resep dokter untuk membuat kaki palsu?',
            'answer' => 'Resep dokter sangat dianjurkan agar spesifikasi alat sesuai rekomendasi medis.',
            'category' => 'Umum',
            'is_active' => true,
            'show_on_home' => true,
        ]);

        Testimonial::create([
            'name' => 'Ahmad Santoso',
            'role_or_info' => 'Pasien Kaki Palsu',
            'content' => 'Pelayanan sangat ramah dan fitting sangat nyaman.',
            'rating' => 5,
            'status' => 'approved',
            'is_active' => true,
            'consent_confirmed' => true,
        ]);
    }

    /**
     * Setiap halaman publik wajib memiliki status 200, title unik (<=70 char),
     * description (70-170 char) unik antar halaman, tepat satu H1, canonical absolut,
     * Open Graph lengkap, dan html lang="id".
     */
    public function test_public_pages_have_valid_on_page_seo_elements(): void
    {
        $urls = [
            'home' => route('home'),
            'about' => route('about'),
            'catalog' => route('catalog.index'),
            'faq' => route('faq'),
            'testimonials' => route('testimonials.index'),
            'appointment' => route('appointment'),
            'privacy' => route('privacy'),
            'product_detail' => route('catalog.show', 'kaki-palsu-bawah-lutut-transtibial'),
            'branch_detail' => route('branch.show', 'sam-cabang-solo'),
        ];

        $titles = [];
        $descriptions = [];

        foreach ($urls as $pageKey => $url) {
            $response = $this->get($url);
            $response->assertStatus(200);

            $html = $response->getContent();

            // 1. html lang="id"
            $this->assertMatchesRegularExpression('/<html[^>]*lang=["\']id["\']/i', $html, "Page {$pageKey} must have lang=id");

            // 2. Title unik & <= 70 karakter
            preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch);
            $this->assertNotEmpty($titleMatch[1] ?? '', "Page {$pageKey} must have a <title>");
            $title = trim(html_entity_decode($titleMatch[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $this->assertLessThanOrEqual(70, mb_strlen($title), "Page {$pageKey} title must be <= 70 chars (got ".mb_strlen($title).': '.$title.')');
            $this->assertNotContains($title, $titles, "Page {$pageKey} title must be unique across pages (duplicate: {$title})");
            $titles[] = $title;

            // 3. Description unik & 70-170 karakter
            preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $html, $descMatch);
            $this->assertNotEmpty($descMatch[1] ?? '', "Page {$pageKey} must have a meta description");
            $desc = trim(html_entity_decode($descMatch[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $this->assertGreaterThanOrEqual(70, mb_strlen($desc), "Page {$pageKey} description must be >= 70 chars (got ".mb_strlen($desc).': '.$desc.')');
            $this->assertLessThanOrEqual(170, mb_strlen($desc), "Page {$pageKey} description must be <= 170 chars (got ".mb_strlen($desc).': '.$desc.')');
            $this->assertNotContains($desc, $descriptions, "Page {$pageKey} description must be unique across pages");
            $descriptions[] = $desc;

            // 4. Tepat SATU <h1>
            preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $html, $h1Matches);
            $this->assertCount(1, $h1Matches[1], "Page {$pageKey} must have exactly one <h1>");

            // 5. Canonical absolut
            preg_match('/<link[^>]*rel=["\']canonical["\'][^>]*href=["\'](.*?)["\']/is', $html, $canonMatch);
            $this->assertNotEmpty($canonMatch[1] ?? '', "Page {$pageKey} must have canonical URL");
            $this->assertTrue((bool) filter_var($canonMatch[1], FILTER_VALIDATE_URL), "Page {$pageKey} canonical must be valid absolute URL");

            // 6. Open Graph & Twitter Card
            $this->assertMatchesRegularExpression('/<meta[^>]*property=["\']og:title["\']/i', $html, "Page {$pageKey} must have og:title");
            $this->assertMatchesRegularExpression('/<meta[^>]*property=["\']og:description["\']/i', $html, "Page {$pageKey} must have og:description");
            $this->assertMatchesRegularExpression('/<meta[^>]*property=["\']og:url["\']/i', $html, "Page {$pageKey} must have og:url");
            $this->assertMatchesRegularExpression('/<meta[^>]*property=["\']og:image["\']/i', $html, "Page {$pageKey} must have og:image");
            $this->assertMatchesRegularExpression('/<meta[^>]*property=["\']og:locale["\'][^>]*content=["\']id_ID["\']/i', $html, "Page {$pageKey} must have og:locale=id_ID");
            $this->assertMatchesRegularExpression('/<meta[^>]*name=["\']twitter:card["\']/i', $html, "Page {$pageKey} must have twitter:card");
        }
    }

    /**
     * Halaman konfirmasi dan admin panel wajib noindex (meta + header),
     * sedangkan produk nonaktif mengembalikan status 404 asli.
     */
    public function test_admin_and_confirmation_pages_have_noindex_meta_and_headers(): void
    {
        // 1. Konfirmasi janji: noindex di meta dan header
        $confirmationResponse = $this->get('/janji/konfirmasi/TEST-CONFIRM-CODE');
        $confirmationResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $confirmationResponse->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        // 2. Admin panel login: noindex header
        $adminResponse = $this->get('/kelola-sam/login');
        $adminResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        // 3. Produk nonaktif: status 404 asli
        $inactiveResponse = $this->get('/katalog/kaki-palsu-nonaktif');
        $inactiveResponse->assertStatus(404);
        $inactiveResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * sitemap.xml: XML valid, hanya URL absolut, memuat produk/cabang/kategori aktif,
     * tidak memuat nonaktif, admin, konfirmasi, atau pencarian.
     */
    public function test_sitemap_xml_contains_only_active_absolute_urls_and_excludes_sensitive_routes(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', (string) $response->headers->get('Content-Type'));

        $content = $response->getContent();

        // Validasi XML syntax
        $xml = simplexml_load_string($content);
        $this->assertNotFalse($xml, 'sitemap.xml must be valid XML');

        // Memuat halaman statis & entitas aktif
        $response->assertSee(route('home'), false);
        $response->assertSee(route('about'), false);
        $response->assertSee(route('catalog.index'), false);
        $response->assertSee(route('catalog.show', 'kaki-palsu-bawah-lutut-transtibial'), false);
        $response->assertSee(route('branch.show', 'sam-cabang-solo'), false);

        // TIDAK memuat: produk nonaktif, admin, konfirmasi, atau query pencarian
        $response->assertDontSee('kaki-palsu-nonaktif', false);
        $response->assertDontSee('/kelola-sam', false);
        $response->assertDontSee('/janji/konfirmasi', false);
        $response->assertDontSee('?q=', false);

        // Semua <loc> wajib URL absolut
        foreach ($xml->url as $urlNode) {
            $loc = (string) $urlNode->loc;
            $this->assertTrue((bool) filter_var($loc, FILTER_VALIDATE_URL), "Sitemap <loc> must be absolute URL: {$loc}");
            $this->assertNotEmpty((string) $urlNode->lastmod, "Sitemap <loc> must have lastmod: {$loc}");
        }
    }

    /**
     * robots.txt: dinamis berdasarkan environment
     * - Production: Allow: /, Disallow: /kelola-sam/, /api/, /janji/, /storage/app/private/, dan Sitemap absolut
     * - Non-production: Disallow: /
     */
    public function test_robots_txt_differs_by_environment(): void
    {
        // 1. Non-production (local / testing)
        config(['app.env' => 'local']);
        $localResponse = $this->get('/robots.txt');
        $localResponse->assertStatus(200);
        $localContent = $localResponse->getContent();
        $this->assertStringContainsString('Disallow: /', $localContent);
        $this->assertStringContainsString('Disallow: /kelola-sam/', $localContent);

        // 2. Production
        config(['app.env' => 'production']);
        $prodResponse = $this->get('/robots.txt');
        $prodResponse->assertStatus(200);
        $prodContent = $prodResponse->getContent();
        $this->assertStringContainsString('Allow: /', $prodContent);
        $this->assertStringContainsString('Disallow: /kelola-sam/', $prodContent);
        $this->assertStringContainsString('Disallow: /api/', $prodContent);
        $this->assertStringContainsString('Disallow: /janji/', $prodContent);
        $this->assertStringContainsString('Disallow: /storage/app/private/', $prodContent);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $prodContent);
    }

    /**
     * JSON-LD: validitas skema, MedicalBusiness, FAQPage, LocalBusiness jadwal (tanpa hari tutup),
     * dan Product tanpa blok offers jika harga tidak ditampilkan.
     */
    public function test_structured_data_json_ld_is_valid_and_consistent(): void
    {
        // 1. Home: MedicalBusiness & WebSite
        $homeResponse = $this->get('/');
        $homeHtml = $homeResponse->getContent();
        $this->assertMatchesRegularExpression('/<script type="application\/ld\+json">(.*?)<\/script>/s', $homeHtml);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $homeHtml, $match);
        $homeData = json_decode($match[1], true);
        $this->assertIsArray($homeData);
        $this->assertEquals('https://schema.org', $homeData['@context']);

        // 2. FAQ: FAQPage cocok dengan pertanyaan aktif
        $faqResponse = $this->get('/faq');
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $faqResponse->getContent(), $faqMatch);
        $faqData = json_decode($faqMatch[1], true);
        $this->assertIsArray($faqData);
        $types = array_column($faqData['@graph'] ?? [$faqData], '@type');
        $this->assertContains('FAQPage', $types);

        // 3. Cabang: LocalBusiness membuka jam operasional dan TIDAK memasukkan hari Minggu (tutup)
        $branchResponse = $this->get(route('branch.show', 'sam-cabang-solo'));
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $branchResponse->getContent(), $branchMatch);
        $branchData = json_decode($branchMatch[1], true);
        $this->assertIsArray($branchData);

        $localBiz = null;
        foreach ($branchData['@graph'] as $item) {
            if ($item['@type'] === 'LocalBusiness') {
                $localBiz = $item;
                break;
            }
        }
        $this->assertNotNull($localBiz);
        $openingSpecs = $localBiz['openingHoursSpecification'] ?? [];
        $daysOpen = array_column($openingSpecs, 'dayOfWeek');
        $this->assertContains('Monday', $daysOpen);
        $this->assertNotContains('Sunday', $daysOpen);

        // 4. Detail Produk: Product schema tanpa offers/price bila harga publik tidak ditampilkan
        $productResponse = $this->get(route('catalog.show', 'kaki-palsu-bawah-lutut-transtibial'));
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $productResponse->getContent(), $productMatch);
        $productData = json_decode($productMatch[1], true);
        $this->assertIsArray($productData);

        $prodSchema = null;
        foreach ($productData['@graph'] as $item) {
            if ($item['@type'] === 'Product') {
                $prodSchema = $item;
                break;
            }
        }
        $this->assertNotNull($prodSchema);
        $this->assertEquals('Kaki Palsu Bawah Lutut Transtibial', $prodSchema['name']);
        $this->assertArrayNotHasKey('offers', $prodSchema, 'Product schema must NOT have offers/price when public prices are unlisted');
    }

    /**
     * Redirects: slug lama -> 301 ke slug baru, huruf besar -> 301 ke huruf kecil,
     * trailing slash konsisten.
     */
    public function test_seo_redirects_for_old_slugs_uppercase_and_trailing_slashes(): void
    {
        // 1. Slug lama di tabel redirects -> 301
        Redirect::create([
            'source_path' => '/katalog/kaki-palsu-lama-banget',
            'target_path' => '/katalog/kaki-palsu-bawah-lutut-transtibial',
            'status_code' => 301,
        ]);

        $redirectResponse = $this->get('/katalog/kaki-palsu-lama-banget');
        $redirectResponse->assertStatus(301);
        $redirectResponse->assertRedirect(url('/katalog/kaki-palsu-bawah-lutut-transtibial'));

        // 2. Huruf kapital pada URL -> 301 ke huruf kecil
        $upperResponse = $this->get('/KATALOG');
        $upperResponse->assertStatus(301);
        $upperResponse->assertRedirect(url('/katalog'));

        // 3. Trailing slash -> 301 ke URL tanpa trailing slash
        $request = Request::create('/about/', 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(url('/about'), $response->headers->get('Location'));
    }

    /**
     * Parameter pencarian internal: canonical ke /katalog dan noindex meta + header.
     */
    public function test_catalog_search_has_noindex_and_clean_canonical(): void
    {
        $searchResponse = $this->get('/katalog?q=kaki');
        $searchResponse->assertStatus(200);

        // noindex meta dan header
        $searchResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $searchResponse->assertSee('<meta name="robots" content="noindex, follow">', false);

        // Canonical bersih ke /katalog
        $searchResponse->assertSee('<link rel="canonical" href="'.url('/katalog').'">', false);
    }

    /**
     * Semua URL canonical, sitemap, Open Graph, dan JSON-LD dibentuk dinamis dari config('app.url').
     * Tidak ada domain hardcode atau 127.0.0.1/localhost yang tertinggal.
     */
    public function test_all_seo_urls_dynamically_follow_custom_app_url(): void
    {
        $customAppUrl = 'https://sam-custom-domain.co.id';
        config(['app.url' => $customAppUrl]);
        URL::forceRootUrl($customAppUrl);

        // 1. Halaman Home
        $homeResponse = $this->get($customAppUrl);
        $homeHtml = $homeResponse->getContent();
        $homeResponse->assertSee('<link rel="canonical" href="'.$customAppUrl.'">', false);
        $homeResponse->assertSee('<meta property="og:url" content="'.$customAppUrl.'">', false);
        $homeResponse->assertSee('"url":"'.$customAppUrl.'"', false);

        // 2. sitemap.xml
        $sitemapResponse = $this->get('/sitemap.xml');
        $sitemapContent = $sitemapResponse->getContent();
        $this->assertStringContainsString('<loc>'.$customAppUrl.'</loc>', $sitemapContent);
        $this->assertStringContainsString('<loc>'.$customAppUrl.'/about</loc>', $sitemapContent);
        $this->assertStringNotContainsString('127.0.0.1', $sitemapContent);
        $this->assertStringNotContainsString('localhost', $sitemapContent);
    }
}
