<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Services\HomePageData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_setting_returns_seeded_photos(): void
    {
        SiteSetting::set('general.gallery', [
            [
                'image' => 'gallery/hero-clinic.webp',
                'title' => 'Fasilitas Workshop Mandiri',
                'alt' => 'Ruang Workshop & Klinik SAM Orthotic Prosthetic',
            ],
            [
                'image' => 'gallery/consultation.webp',
                'title' => 'Pemeriksaan & Konsultasi',
                'alt' => 'Sesi Konsultasi Kebutuhan Alat Gerak Pasien',
            ],
            [
                'image' => 'gallery/fitting.webp',
                'title' => 'Fitting & Evaluasi Gerak',
                'alt' => 'Sesi Fitting dan Penyesuaian Alat Pasien',
            ],
        ], 'json', 'general');

        $photos = HomePageData::get()['galleryPhotos'];

        $this->assertIsArray($photos);
        $this->assertCount(3, $photos);
        $this->assertEquals('Fasilitas Workshop Mandiri', $photos[0]['title']);
        $this->assertEquals('Pemeriksaan & Konsultasi', $photos[1]['title']);
        $this->assertEquals('Fitting & Evaluasi Gerak', $photos[2]['title']);
    }

    public function test_deleting_photos_in_admin_does_not_resurrect_fallback_photos(): void
    {
        // Admin deletes all photos from the gallery
        SiteSetting::set('general.gallery', [], 'json', 'general');

        $photos = HomePageData::get()['galleryPhotos'];

        $this->assertIsArray($photos);
        $this->assertEmpty($photos);
    }

    public function test_editing_gallery_photos_is_reflected_immediately(): void
    {
        SiteSetting::set('general.gallery', [
            [
                'image' => 'gallery/custom-photo.webp',
                'title' => 'Foto Baru Diedit',
                'alt' => 'Deskripsi Baru',
            ],
        ], 'json', 'general');

        $photos = HomePageData::get()['galleryPhotos'];

        $this->assertCount(1, $photos);
        $this->assertEquals('Foto Baru Diedit', $photos[0]['title']);
        $this->assertEquals('Deskripsi Baru', $photos[0]['alt']);
    }
}
