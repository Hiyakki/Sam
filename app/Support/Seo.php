<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Seo
{
    protected string $title = '';

    protected string $description = '';

    protected ?string $canonical = null;

    protected string $robots = 'index, follow';

    protected string $ogType = 'website';

    protected ?string $ogImage = null;

    protected string $siteName = 'SAM Orthotic Prosthetic';

    /**
     * @var array<int, array{name: string, url: string|null}>
     */
    protected array $breadcrumbs = [];

    protected array $settings = [];

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? SiteSetting::allCached();
        $this->siteName = $this->settings['general.site_name'] ?? 'SAM Orthotic Prosthetic';
    }

    public static function make(?array $settings = null): self
    {
        return new self($settings);
    }

    /**
     * Potong teks rapi pada batas kata tanpa memotong di tengah kata.
     */
    public static function cleanSnippet(?string $text, int $max = 160, int $min = 120): string
    {
        if (blank($text)) {
            return '';
        }

        $clean = preg_replace('/\s+/', ' ', trim(strip_tags((string) $text))) ?? '';

        if (mb_strlen($clean) <= $max) {
            return $clean;
        }

        $cut = mb_substr($clean, 0, $max);
        $lastSpace = mb_strrpos($cut, ' ');

        if ($lastSpace !== false && $lastSpace >= $min) {
            $result = mb_substr($cut, 0, $lastSpace);
        } else {
            $result = $cut;
        }

        return rtrim($result, ' ,.;:-').'...';
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return 'Kaki Palsu, Tangan Palsu & Orthosis | '.$this->siteName;
    }

    public function setDescription(string $description): self
    {
        $this->description = self::cleanSnippet($description, 160, 120);

        return $this;
    }

    public function getDescription(): string
    {
        if (filled($this->description)) {
            return $this->description;
        }

        return 'Layanan pembuatan alat bantu ortotik (penyangga/brace) dan prostetik (kaki & tangan palsu) berkualitas dengan fitting presisi dan tenaga berpengalaman sejak 2014.';
    }

    public function setCanonical(?string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    public function getCanonical(): string
    {
        if (filled($this->canonical)) {
            return $this->canonical;
        }

        return self::buildCleanUrl(request());
    }

    public function setRobots(string $robots): self
    {
        $this->robots = $robots;

        return $this;
    }

    public function getRobots(): string
    {
        return $this->robots;
    }

    public function setOgType(string $type): self
    {
        $this->ogType = $type;

        return $this;
    }

    public function getOgType(): string
    {
        return $this->ogType;
    }

    public function setOgImage(?string $image): self
    {
        $this->ogImage = $image;

        return $this;
    }

    public function getOgImage(): string
    {
        if (filled($this->ogImage)) {
            return filter_var($this->ogImage, FILTER_VALIDATE_URL)
                ? $this->ogImage
                : url($this->ogImage);
        }

        $configured = $this->settings['seo.default_og_image'] ?? null;
        if (filled($configured)) {
            return filter_var($configured, FILTER_VALIDATE_URL)
                ? $configured
                : url('storage/'.ltrim($configured, '/'));
        }

        return url('images/logo-sam.png');
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    /**
     * @param  array<int, array{name: string, url: string|null}>  $breadcrumbs
     */
    public function setBreadcrumbs(array $breadcrumbs): self
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    /**
     * @return array<int, array{name: string, url: string|null}>
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    // =========================================================================
    // Factory presets per halaman publik
    // =========================================================================

    public function forHome(): self
    {
        $title = $this->settings['seo.home_title'] ?? 'Kaki Palsu, Tangan Palsu & Orthosis | '.$this->siteName;
        $description = $this->settings['seo.home_description'] ?? 'Layanan pembuatan kaki palsu, tangan palsu, dan alat penyangga tubuh (orthosis) presisi sejak 2014. Konsultasikan kebutuhan mobilitas Anda bersama kami.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/'))
            ->setOgType('website')
            ->setBreadcrumbs([]);

        return $this;
    }

    public function forAbout(): self
    {
        $title = $this->settings['seo.about_title'] ?? 'Tentang SAM | '.$this->siteName;
        $description = $this->settings['seo.about_description'] ?? 'Mengenal SAM Orthotic Prosthetic, pusat perancangan dan fabrikasi kustom alat bantu gerak tubuh ortotik dan prostetik tepercaya sejak 2014.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/about'))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Tentang Kami', 'url' => null],
            ]);

        return $this;
    }

    public function forCatalog(?ProductCategory $currentCategory = null, ?string $search = null, ?int $page = null): self
    {
        $baseCatalogUrl = url('/katalog');

        if (filled($search)) {
            $title = 'Pencarian: '.Str::limit($search, 30).' | Katalog | '.$this->siteName;
            $description = 'Hasil pencarian untuk "'.e($search).'" di katalog alat ortotik dan prostetik SAM. Temukan berbagai jenis kaki palsu, tangan palsu, dan brace medis.';

            $this->setTitle($title)
                ->setDescription($description)
                ->setCanonical($baseCatalogUrl)
                ->setRobots('noindex, follow')
                ->setBreadcrumbs([
                    ['name' => 'Beranda', 'url' => url('/')],
                    ['name' => 'Katalog', 'url' => $baseCatalogUrl],
                    ['name' => 'Pencarian', 'url' => null],
                ]);

            return $this;
        }

        if ($currentCategory) {
            $catTitle = filled($currentCategory->meta_title)
                ? $currentCategory->meta_title
                : $currentCategory->name.' | Katalog | '.$this->siteName;

            $catDesc = filled($currentCategory->meta_description)
                ? $currentCategory->meta_description
                : (filled($currentCategory->intro_text)
                    ? self::cleanSnippet($currentCategory->intro_text, 160, 120)
                    : 'Koleksi produk '.$currentCategory->name.' kustom dengan fitting presisi dan bahan berkualitas dari SAM Orthotic Prosthetic.');

            $canonical = $baseCatalogUrl.'?kategori='.urlencode($currentCategory->slug);
            if ($page && $page > 1) {
                $canonical .= '&page='.$page;
            }

            $breadcrumbs = [
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Katalog', 'url' => $baseCatalogUrl],
            ];

            if ($currentCategory->parent) {
                $breadcrumbs[] = [
                    'name' => $currentCategory->parent->name,
                    'url' => $baseCatalogUrl.'?kategori='.urlencode($currentCategory->parent->slug),
                ];
            }

            $breadcrumbs[] = ['name' => $currentCategory->name, 'url' => null];

            $this->setTitle($catTitle)
                ->setDescription($catDesc)
                ->setCanonical($canonical)
                ->setBreadcrumbs($breadcrumbs);

            return $this;
        }

        $title = $this->settings['seo.catalog_title'] ?? 'Katalog Alat Orthotic & Prosthetic | '.$this->siteName;
        $description = $this->settings['seo.catalog_description'] ?? 'Daftar produk kaki palsu, tangan palsu, korset tulang belakang, dan brace kustom dengan spesifikasi dan indikasi medis lengkap.';

        $canonical = $baseCatalogUrl;
        if ($page && $page > 1) {
            $canonical .= '?page='.$page;
        }

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical($canonical)
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Katalog', 'url' => null],
            ]);

        return $this;
    }

    public function forProduct(Product $product): self
    {
        $brandSuffix = ' | '.$this->siteName;
        $maxLen = 65;
        $availableForNameAndFunction = $maxLen - mb_strlen($brandSuffix);

        $shortFunction = '';
        if (filled($product->function)) {
            $remaining = $availableForNameAndFunction - mb_strlen($product->name) - 3;
            if ($remaining >= 12) {
                $shortFunction = ' - '.Str::limit($product->function, $remaining, '');
            }
        }

        $fullName = $product->name.$shortFunction;
        if (mb_strlen($fullName) > $availableForNameAndFunction) {
            $fullName = Str::limit($product->name, $availableForNameAndFunction, '');
        }

        $defaultTitle = $fullName.$brandSuffix;
        $title = filled($product->meta_title) ? $product->meta_title : $defaultTitle;

        if (filled($product->meta_description)) {
            $descSource = $product->meta_description;
        } else {
            $parts = array_filter([
                $product->name,
                $product->function,
                $product->indication,
                strip_tags((string) $product->description),
            ]);
            $descText = implode('. ', $parts);
            if (mb_strlen($descText) < 120) {
                $descText .= '. Fabrikasi kustom presisi di SAM Orthotic Prosthetic.';
            }
            $descSource = $descText;
        }

        $description = self::cleanSnippet($descSource, 160, 120);

        $imageUrl = $product->photo ? url('storage/'.ltrim($product->photo, '/')) : null;

        $breadcrumbs = [
            ['name' => 'Beranda', 'url' => url('/')],
            ['name' => 'Katalog', 'url' => url('/katalog')],
        ];

        if ($product->category) {
            $breadcrumbs[] = [
                'name' => $product->category->name,
                'url' => url('/katalog?kategori='.urlencode($product->category->slug)),
            ];
        }

        $breadcrumbs[] = ['name' => $product->name, 'url' => null];

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/katalog/'.$product->slug))
            ->setOgType('product')
            ->setOgImage($imageUrl)
            ->setBreadcrumbs($breadcrumbs);

        return $this;
    }

    public function forFaq(): self
    {
        $title = $this->settings['seo.faq_title'] ?? 'Pertanyaan Umum | '.$this->siteName;
        $description = $this->settings['seo.faq_description'] ?? 'Jawaban seputar prosedur pemesanan, resep dokter, proses fitting, durasi pembuatan, dan perawatan alat bantu gerak ortotik prostetik.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/faq'))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'FAQ', 'url' => null],
            ]);

        return $this;
    }

    public function forTestimonials(): self
    {
        $title = $this->settings['seo.testimonials_title'] ?? 'Testimoni Pasien | '.$this->siteName;
        $description = $this->settings['seo.testimonials_description'] ?? 'Pengalaman dan cerita pasien dalam menggunakan alat ortotik dan prostetik kustom dari SAM Orthotic Prosthetic.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/testimoni'))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Testimoni', 'url' => null],
            ]);

        return $this;
    }

    public function forAppointment(): self
    {
        $title = $this->settings['seo.appointment_title'] ?? 'Buat Janji Konsultasi | '.$this->siteName;
        $description = $this->settings['seo.appointment_description'] ?? 'Pilih cabang dan jadwal konsultasi tatap muka bersama tenaga ahli ortotik prostetik kami secara langsung.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/appointment'))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Appointment', 'url' => null],
            ]);

        return $this;
    }

    public function forAppointmentSuccess(): self
    {
        $this->setTitle('Pendaftaran Janji Temu Berhasil | '.$this->siteName)
            ->setDescription('Pendaftaran janji temu pasien SAM Orthotic Prosthetic telah diterima.')
            ->setCanonical(url('/janji/berhasil'))
            ->setRobots('noindex, nofollow');

        return $this;
    }

    public function forConfirmation(): self
    {
        return $this->forAppointmentSuccess();
    }

    public function forPrivacy(): self
    {
        $title = $this->settings['seo.privacy_title'] ?? 'Kebijakan Privasi | '.$this->siteName;
        $description = $this->settings['seo.privacy_description'] ?? 'Kebijakan perlindungan data pribadi dan resep medis pasien di platform SAM Orthotic Prosthetic.';

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/kebijakan-privasi'))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Kebijakan Privasi', 'url' => null],
            ]);

        return $this;
    }

    public function forBranch(string $branchName, string $branchSlug, ?string $address = null): self
    {
        $title = 'Cabang '.$branchName.' | '.$this->siteName;
        $description = self::cleanSnippet(
            'Informasi lokasi, jam operasional, layanan, dan kontak klinik SAM Orthotic Prosthetic Cabang '.$branchName.($address ? ('. Alamat: '.$address) : '.'),
            160,
            120
        );

        $this->setTitle($title)
            ->setDescription($description)
            ->setCanonical(url('/cabang/'.$branchSlug))
            ->setBreadcrumbs([
                ['name' => 'Beranda', 'url' => url('/')],
                ['name' => 'Tentang Kami', 'url' => url('/about')],
                ['name' => 'Cabang '.$branchName, 'url' => null],
            ]);

        return $this;
    }

    public function for404(): self
    {
        $this->setTitle('Halaman Tidak Ditemukan (404) | '.$this->siteName)
            ->setDescription('Halaman yang Anda tuju tidak ditemukan atau telah dipindahkan.')
            ->setRobots('noindex, nofollow');

        return $this;
    }

    /**
     * Membentuk URL bersih tanpa tracking params (utm_*, gclid, fbclid).
     */
    public static function buildCleanUrl(Request $request): string
    {
        $url = url($request->path());

        // Parameter yang diizinkan untuk canonical (seperti kategori, page)
        $allowedParams = ['kategori', 'page'];
        $filtered = [];

        foreach ($allowedParams as $param) {
            if ($request->has($param) && filled($request->query($param))) {
                $filtered[$param] = $request->query($param);
            }
        }

        if (! empty($filtered)) {
            $url .= '?'.http_build_query($filtered);
        }

        return $url;
    }
}
