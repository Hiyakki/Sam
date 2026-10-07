<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

class HomePageData
{
    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $settings = SiteSetting::allCached();

        // 1. Root categories with total count (direct + all descendants) & representative photo
        $categories = ProductCategory::roots()
            ->with([
                'children' => fn ($q) => $q->withCount(['products' => fn ($p) => $p->where('is_active', true)]),
                'products' => fn ($p) => $p->where('is_active', true)->whereNotNull('photo')->take(1),
            ])
            ->withCount(['products' => fn ($p) => $p->where('is_active', true)])
            ->get()
            ->map(function ($cat) {
                $childrenCount = $cat->children->sum('products_count');
                $cat->total_products_count = $cat->products_count + $childrenCount;

                // Pick representative photo from direct products or first child product
                $directPhoto = $cat->products->first()?->photo;
                if (! $directPhoto) {
                    $childWithPhoto = Product::whereIn('category_id', $cat->children->pluck('id'))
                        ->where('is_active', true)
                        ->whereNotNull('photo')
                        ->first();
                    $cat->representative_photo = $childWithPhoto?->photo;
                } else {
                    $cat->representative_photo = $directPhoto;
                }

                return $cat;
            });

        // 2. Featured active products (6-8 items with category)
        $featuredProducts = Product::active()
            ->with('category')
            ->orderByRaw('photo IS NULL') // Prioritaskan yang ada fotonya
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        // 3. 3-Step Service flow
        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        // 4. Testimonials (active and consent_confirmed enforced by scopeActive)
        $testimonials = Testimonial::active()
            ->with('product')
            ->take(6)
            ->get();

        // 5. FAQs on home
        $homeFaqs = Faq::onHome()
            ->take(6)
            ->get();

        // 6. Active branches with schedules
        $branches = Branch::active()
            ->with(['schedules' => fn ($q) => $q->orderBy('day_of_week')])
            ->take(4)
            ->get();

        // 7. Verified factual numbers
        $stats = [
            'since_year' => 2014,
            'branches_count' => Branch::where('is_active', true)->count(),
            'products_count' => Product::where('is_active', true)->count(),
            'services_count' => Service::where('is_active', true)->count(),
        ];

        // 8. Gallery photos if present in storage/app/public/site/
        $galleryPhotos = self::getGalleryPhotos();

        return compact(
            'settings',
            'categories',
            'featuredProducts',
            'services',
            'testimonials',
            'homeFaqs',
            'branches',
            'stats',
            'galleryPhotos'
        );
    }

    /**
     * @return array<int, array{path: string, alt: string, title: string}>
     */
    protected static function getGalleryPhotos(): array
    {
        $sitePhotos = [
            [
                'file' => 'site/hero-clinic.webp',
                'alt' => 'Ruang Workshop & Klinik SAM Orthotic Prosthetic',
                'title' => 'Fasilitas Workshop Mandiri',
            ],
            [
                'file' => 'site/consultation.webp',
                'alt' => 'Sesi Konsultasi Kebutuhan Alat Gerak Pasien',
                'title' => 'Pemeriksaan & Konsultasi',
            ],
            [
                'file' => 'site/fitting.webp',
                'alt' => 'Sesi Fitting dan Penyesuaian Alat Pasien',
                'title' => 'Fitting & Evaluasi Gerak',
            ],
            [
                'file' => 'site/workshop.webp',
                'alt' => 'Proses Fabrikasi Komponen Prostetik',
                'title' => 'Fabrikasi Presisi',
            ],
            [
                'file' => 'site/examination.webp',
                'alt' => 'Penyesuaian Anatomi dan Pengukuran Pasien',
                'title' => 'Pemeriksaan Biomekanik',
            ],
        ];

        $disk = Storage::disk('public');
        $valid = [];

        foreach ($sitePhotos as $photo) {
            if ($disk->exists($photo['file'])) {
                $valid[] = [
                    'path' => $disk->url($photo['file']),
                    'alt' => $photo['alt'],
                    'title' => $photo['title'],
                ];
            }
        }

        return $valid;
    }
}
