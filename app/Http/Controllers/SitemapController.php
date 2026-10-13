<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Render sitemap.xml utama.
     */
    public function sitemap(): Response
    {
        // 1. URL Halaman Statis Publik
        $latestSettingUpdate = rescue(fn () => SiteSetting::max('updated_at'), null, false);
        $defaultLastmod = $latestSettingUpdate ? Carbon::parse($latestSettingUpdate)->toIso8601String() : now()->toIso8601String();

        $staticPages = [
            [
                'loc' => url('/'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => url('/about'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/katalog'),
                'lastmod' => rescue(fn () => Product::active()->max('updated_at') ? Carbon::parse(Product::active()->max('updated_at'))->toIso8601String() : $defaultLastmod, $defaultLastmod, false),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => url('/faq'),
                'lastmod' => rescue(fn () => Faq::active()->max('updated_at') ? Carbon::parse(Faq::active()->max('updated_at'))->toIso8601String() : $defaultLastmod, $defaultLastmod, false),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => url('/testimoni'),
                'lastmod' => rescue(fn () => Testimonial::where('status', 'approved')->where('is_active', true)->max('updated_at') ? Carbon::parse(Testimonial::where('status', 'approved')->where('is_active', true)->max('updated_at'))->toIso8601String() : $defaultLastmod, $defaultLastmod, false),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ],
            [
                'loc' => url('/appointment'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
            [
                'loc' => url('/kebijakan-privasi'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ],
        ];

        // 2. Cabang Aktif
        $branches = Branch::active()
            ->select(['slug', 'updated_at'])
            ->get()
            ->map(fn ($branch) => [
                'loc' => url('/cabang/'.$branch->slug),
                'lastmod' => $branch->updated_at?->toIso8601String() ?? $defaultLastmod,
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ])
            ->all();

        // 3. Kategori Aktif (yang memiliki produk aktif)
        $categories = ProductCategory::whereHas('products', fn ($q) => $q->where('is_active', true))
            ->orWhereHas('children.products', fn ($q) => $q->where('is_active', true))
            ->select(['id', 'slug', 'updated_at'])
            ->get()
            ->map(fn ($cat) => [
                'loc' => url('/katalog?kategori='.$cat->slug),
                'lastmod' => $cat->updated_at?->toIso8601String() ?? $defaultLastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ])
            ->all();

        // 4. Produk Aktif
        $products = Product::active()
            ->select(['id', 'slug', 'updated_at'])
            ->get()
            ->map(fn ($product) => [
                'loc' => url('/katalog/'.$product->slug),
                'lastmod' => $product->updated_at?->toIso8601String() ?? $defaultLastmod,
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ])
            ->all();

        $allEntries = array_merge($staticPages, $branches, $categories, $products);

        // Jika URL melebihi batas 50.000 URL, struktur sitemap index disiapkan
        if (count($allEntries) > 50000) {
            return $this->sitemapIndex();
        }

        $content = view('seo.sitemap', ['entries' => $allEntries])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Struktur Sitemap Index untuk skala > 50.000 URL.
     */
    protected function sitemapIndex(): Response
    {
        $content = view('seo.sitemap-index', [
            'sitemaps' => [
                url('/sitemap.xml'),
            ],
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Render robots.txt dinamis berdasarkan environment aplikasi.
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');
        $isProduction = app()->environment('production');

        $commonDirectives = "User-agent: *\n"
            ."Disallow: /kelola-sam/\n"
            ."Disallow: /api/\n"
            ."Disallow: /janji/\n"
            ."Disallow: /storage/app/private/\n";

        if ($isProduction) {
            $content = $commonDirectives
                ."Allow: /\n\n"
                ."Sitemap: {$sitemapUrl}\n";
        } else {
            // Environment non-production (local, staging, testing)
            $content = $commonDirectives
                ."Disallow: /\n\n"
                ."Sitemap: {$sitemapUrl}\n";
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
