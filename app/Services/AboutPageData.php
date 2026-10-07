<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class AboutPageData
{
    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $settings = SiteSetting::allCached();

        $branches = Branch::active()
            ->with([
                'schedules' => fn ($q) => $q->orderBy('day_of_week'),
                'services',
            ])
            ->get();

        $stats = [
            'since_year' => 2014,
            'branches_count' => Branch::where('is_active', true)->count(),
            'products_count' => Product::where('is_active', true)->count(),
            'services_count' => Service::where('is_active', true)->count(),
        ];

        // Format and decode json items safely
        $milestones = is_array($settings['about.milestones'] ?? null)
            ? $settings['about.milestones']
            : (is_string($settings['about.milestones'] ?? null) ? json_decode($settings['about.milestones'], true) ?? [] : []);

        $values = is_array($settings['about.values'] ?? null)
            ? $settings['about.values']
            : (is_string($settings['about.values'] ?? null) ? json_decode($settings['about.values'], true) ?? [] : []);

        $team = is_array($settings['about.team'] ?? null)
            ? $settings['about.team']
            : (is_string($settings['about.team'] ?? null) ? json_decode($settings['about.team'], true) ?? [] : []);

        $mission = is_array($settings['about.mission'] ?? null)
            ? $settings['about.mission']
            : (is_string($settings['about.mission'] ?? null) ? json_decode($settings['about.mission'], true) ?? [] : []);

        $galleryPhotos = self::getGalleryPhotos();

        return compact(
            'settings',
            'branches',
            'stats',
            'milestones',
            'values',
            'team',
            'mission',
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
