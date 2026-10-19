<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // === HOME ===
            [
                'key' => 'home.hero_title',
                'value' => 'Hidup Lebih Aktif dengan Alat Orthotic & Prosthetic Berkualitas',
                'type' => 'text',
                'group' => 'home',
            ],
            [
                'key' => 'home.hero_subtitle',
                'value' => 'SAM Orthotic Prosthetic hadir sejak 2014 untuk membantu Anda bergerak bebas. Konsultasikan kebutuhan Anda bersama tenaga ahli kami.',
                'type' => 'text',
                'group' => 'home',
            ],
            [
                'key' => 'home.cta_text',
                'value' => 'Buat Janji Sekarang',
                'type' => 'text',
                'group' => 'home',
            ],
            [
                'key' => 'home.advantages',
                'value' => json_encode([
                    ['icon' => 'calendar', 'title' => 'Pengalaman Sejak 2014', 'desc' => 'Telah melayani pembuatan alat ortotik dan prostetik selama lebih dari satu dekade.'],
                    ['icon' => 'user-check', 'title' => 'Pendekatan Kustom', 'desc' => 'Alat dibuat secara individual mengikuti pengukuran fisik dan kebutuhan gerak pasien.'],
                    ['icon' => 'wrench', 'title' => 'Fitting & Penyesuaian', 'desc' => 'Dilakukan evaluasi dan fitting langsung sebelum alat diserahkan kepada pasien.'],
                    ['icon' => 'sparkles', 'title' => 'Bengkel Fabrikasi Mandiri', 'desc' => 'Proses pengerjaan dan modifikasi dilakukan di fasilitas workshop sendiri.'],
                ]),
                'type' => 'json',
                'group' => 'home',
            ],

            // === ABOUT ===
            [
                'key' => 'about.story',
                'value' => '<p>SAM Orthotic Prosthetic didirikan pada tahun 2014 di Bogor dengan komitmen mendampingi para penyandang disabilitas fisik dan pasien pasca-cedera agar dapat kembali bergerak secara mandiri.</p><p>Kami fokus pada perancangan, pembuatan, serta penyesuaian alat bantu penyangga tubuh (ortosis) dan pengganti anggota gerak (prostesis). Melalui pendekatan kustom yang presisi dan proses fitting langsung, kami berupaya memastikan setiap alat yang kami hasilkan nyaman dipakai dalam aktivitas sehari-hari.</p>',
                'type' => 'richtext',
                'group' => 'about',
            ],
            [
                'key' => 'about.vision',
                'value' => 'Menjadi pusat pembuatan alat ortotik dan prostetik kustom yang tepercaya, berorientasi pada kenyamanan pasien, dan mendukung kemandirian gerak di Indonesia.',
                'type' => 'text',
                'group' => 'about',
            ],
            [
                'key' => 'about.mission',
                'value' => json_encode([
                    'Menyediakan alat bantu gerak tubuh ortotik dan prostetik kustom dengan pengukuran dan fitting yang presisi.',
                    'Memberikan konsultasi dan edukasi menyeluruh kepada pasien serta keluarga terkait perawatan alat.',
                    'Mengutamakan kenyamanan, fungsi ergonomis, dan daya tahan alat untuk aktivitas sehari-hari.',
                ]),
                'type' => 'json',
                'group' => 'about',
            ],
            [
                'key' => 'about.milestones',
                'value' => json_encode([
                    ['year' => '2014', 'title' => 'Awal Berdiri', 'desc' => 'Memulai layanan pembuatan alat ortotik dan prostetik untuk membantu pasien bergerak mandiri.'],
                    ['year' => '2018', 'title' => 'Pengembangan Workshop', 'desc' => 'Peningkatan kapasitas bengkel kerja dan alat fabrikasi untuk presisi fitting.'],
                    ['year' => '2023', 'title' => 'Layanan Terpadu', 'desc' => 'Pengembangan kemudahan konsultasi berkala dan sistem reservasi janji temu pasien.'],
                ]),
                'type' => 'json',
                'group' => 'about',
            ],
            [
                'key' => 'about.values',
                'value' => json_encode([
                    ['title' => 'Fokus Pasien', 'desc' => 'Mendengarkan kebutuhan gerak pasien dan merancang alat sesuai kondisi anatomis.'],
                    ['title' => 'Presisi Fabrikasi', 'desc' => 'Pengukuran detail dan proses cetak fitting bertahap demi kenyamanan penggunaan.'],
                    ['title' => 'Transparan & Informatif', 'desc' => 'Memberikan penjelasan jelas mengenai fungsi, penggunaan, serta perawatan alat.'],
                    ['title' => 'Pendampingan Berkelanjutan', 'desc' => 'Mendukung penyesuaian atau evaluasi alat bila terdapat perubahan kondisi fisik pasien.'],
                ]),
                'type' => 'json',
                'group' => 'about',
            ],
            [
                'key' => 'about.team',
                'value' => json_encode([]),
                'type' => 'json',
                'group' => 'about',
            ],

            // === KONTAK ===
            [
                'key' => 'contact.whatsapp',
                'value' => '6281234567890',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'contact.email',
                'value' => 'sam.orthotic@example.com',
                'type' => 'text',
                'group' => 'contact',
            ],
            [
                'key' => 'contact.instagram',
                'value' => '@sam_kakipalsu',
                'type' => 'text',
                'group' => 'contact',
            ],

            // === GENERAL ===
            [
                'key' => 'general.site_name',
                'value' => 'SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'general.privacy_policy',
                'value' => '<h2>Kebijakan Privasi</h2><p>SAM Orthotic Prosthetic berkomitmen melindungi data pribadi Anda. [LENGKAPI: teks kebijakan privasi lengkap dari pemilik, termasuk: data yang dikumpulkan, tujuan, lama penyimpanan, kontak pengelola, cara meminta penghapusan data]</p>',
                'type' => 'richtext',
                'group' => 'general',
            ],
            [
                'key' => 'general.appointment_max_days',
                'value' => '60',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'general.appointment_reschedule_min_hours',
                'value' => '24',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'general.slot_buffer_minutes',
                'value' => '60',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'general.gallery',
                'value' => json_encode([
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
                ]),
                'type' => 'json',
                'group' => 'general',
            ],

            // === SEO ===
            [
                'key' => 'seo.home_title',
                'value' => 'Kaki Palsu, Tangan Palsu & Orthosis | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.home_description',
                'value' => 'Layanan pembuatan kaki palsu, tangan palsu, dan alat penyangga tubuh (orthosis) presisi sejak 2014. Konsultasikan kebutuhan mobilitas Anda bersama kami.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.about_title',
                'value' => 'Tentang SAM | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.about_description',
                'value' => 'Mengenal SAM Orthotic Prosthetic, pusat perancangan dan fabrikasi kustom alat bantu gerak tubuh ortotik dan prostetik tepercaya sejak 2014.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.catalog_title',
                'value' => 'Katalog Alat Orthotic & Prosthetic | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.catalog_description',
                'value' => 'Daftar produk kaki palsu, tangan palsu, korset tulang belakang, dan brace kustom dengan spesifikasi dan indikasi medis lengkap.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.faq_title',
                'value' => 'Pertanyaan Umum | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.faq_description',
                'value' => 'Jawaban seputar prosedur pemesanan, resep dokter, proses fitting, durasi pembuatan, dan perawatan alat bantu gerak ortotik prostetik.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.testimonials_title',
                'value' => 'Testimoni Pasien | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.testimonials_description',
                'value' => 'Pengalaman dan cerita pasien dalam menggunakan alat ortotik dan prostetik kustom dari SAM Orthotic Prosthetic.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.appointment_title',
                'value' => 'Buat Janji Konsultasi | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.appointment_description',
                'value' => 'Pilih cabang dan jadwal konsultasi tatap muka bersama tenaga ahli ortotik prostetik kami secara langsung.',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.privacy_title',
                'value' => 'Kebijakan Privasi | SAM Orthotic Prosthetic',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'seo.privacy_description',
                'value' => 'Kebijakan perlindungan data pribadi dan resep medis pasien di platform SAM Orthotic Prosthetic.',
                'type' => 'text',
                'group' => 'seo',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
