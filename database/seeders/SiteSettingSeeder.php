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
                'key'   => 'home.hero_title',
                'value' => 'Hidup Lebih Aktif dengan Alat Orthotic & Prosthetic Berkualitas',
                'type'  => 'text',
                'group' => 'home',
            ],
            [
                'key'   => 'home.hero_subtitle',
                'value' => 'SAM Orthotic Prosthetic hadir sejak 2014 untuk membantu Anda bergerak bebas. Konsultasikan kebutuhan Anda bersama tenaga ahli kami.',
                'type'  => 'text',
                'group' => 'home',
            ],
            [
                'key'   => 'home.cta_text',
                'value' => 'Buat Janji Sekarang',
                'type'  => 'text',
                'group' => 'home',
            ],
            [
                'key'   => 'home.advantages',
                'value' => json_encode([
                    ['icon' => 'star', 'title' => 'Berpengalaman Sejak 2014', 'desc' => 'Lebih dari 10 tahun melayani pasien orthotic dan prosthetic di Indonesia.'],
                    ['icon' => 'users', 'title' => 'Tenaga Ahli Bersertifikat', 'desc' => 'Tim ortotis prostétis berpengalaman dan berlisensi resmi.'],
                    ['icon' => 'heart', 'title' => 'Pelayanan Personal', 'desc' => 'Setiap alat dibuat sesuai anatomi dan kebutuhan unik masing-masing pasien.'],
                    ['icon' => 'shield', 'title' => 'Material Berkualitas', 'desc' => 'Menggunakan bahan pilihan untuk kenyamanan dan ketahanan jangka panjang.'],
                ]),
                'type'  => 'json',
                'group' => 'home',
            ],

            // === ABOUT ===
            [
                'key'   => 'about.story',
                'value' => 'SAM Orthotic Prosthetic berdiri pada tahun 2014 dengan semangat membantu masyarakat Indonesia yang membutuhkan alat bantu gerak berkualitas. [LENGKAPI: cerita lengkap dari pemilik]',
                'type'  => 'richtext',
                'group' => 'about',
            ],
            [
                'key'   => 'about.vision',
                'value' => 'Menjadi penyedia alat orthotic dan prosthetic terpercaya yang membantu setiap pasien menjalani kehidupan yang lebih aktif dan mandiri. [LENGKAPI]',
                'type'  => 'text',
                'group' => 'about',
            ],
            [
                'key'   => 'about.mission',
                'value' => json_encode([
                    'Memberikan pelayanan orthotic dan prosthetic yang profesional, personal, dan terjangkau.',
                    'Menggunakan material berkualitas tinggi untuk setiap alat yang dibuat.',
                    'Terus meningkatkan kompetensi tim melalui pelatihan dan sertifikasi. [LENGKAPI]',
                ]),
                'type'  => 'json',
                'group' => 'about',
            ],
            [
                'key'   => 'about.team',
                'value' => json_encode([]),
                'type'  => 'json',
                'group' => 'about',
            ],

            // === KONTAK ===
            [
                'key'   => 'contact.whatsapp',
                'value' => '6281234567890',
                'type'  => 'text',
                'group' => 'contact',
            ],
            [
                'key'   => 'contact.email',
                'value' => 'sam.orthotic@example.com',
                'type'  => 'text',
                'group' => 'contact',
            ],
            [
                'key'   => 'contact.instagram',
                'value' => '@sam_kakipalsu',
                'type'  => 'text',
                'group' => 'contact',
            ],

            // === GENERAL ===
            [
                'key'   => 'general.site_name',
                'value' => 'SAM Orthotic Prosthetic',
                'type'  => 'text',
                'group' => 'general',
            ],
            [
                'key'   => 'general.privacy_policy',
                'value' => '<h2>Kebijakan Privasi</h2><p>SAM Orthotic Prosthetic berkomitmen melindungi data pribadi Anda. [LENGKAPI: teks kebijakan privasi lengkap dari pemilik, termasuk: data yang dikumpulkan, tujuan, lama penyimpanan, kontak pengelola, cara meminta penghapusan data]</p>',
                'type'  => 'richtext',
                'group' => 'general',
            ],
            [
                'key'   => 'general.appointment_max_days',
                'value' => '60',
                'type'  => 'text',
                'group' => 'general',
            ],
            [
                'key'   => 'general.appointment_reschedule_min_hours',
                'value' => '24',
                'type'  => 'text',
                'group' => 'general',
            ],
            [
                'key'   => 'general.slot_buffer_minutes',
                'value' => '60',
                'type'  => 'text',
                'group' => 'general',
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
