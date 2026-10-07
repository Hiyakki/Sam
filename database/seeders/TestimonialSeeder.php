<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        // Catatan: semua data contoh dibuat dengan is_active = false agar admin
        // menggantinya dengan testimoni asli yang sudah mendapatkan izin tampil dari pasien.
        // Jangan aktifkan tanpa izin tertulis dari pasien yang bersangkutan.
        $testimonials = [
            [
                'name'              => 'Ibu R.',
                'role_or_info'      => 'Pengguna kaki palsu (Transtibial Prosthesis)',
                'product_id'        => null,
                'content'           => '[Ini adalah contoh testimoni. Ganti dengan testimoni asli dari pasien yang sudah memberikan izin tampil. Contoh: "Alat kaki palsu dari SAM sangat nyaman dan membantu saya beraktivitas kembali..."]',
                'rating'            => null,
                'photo'             => null,
                'consent_confirmed' => false, // WAJIB diisi admin setelah mendapatkan izin asli
                'is_active'         => false, // Tidak tampil di publik sampai diaktifkan admin
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Bapak S.',
                'role_or_info'      => 'Orang tua pasien skoliosis',
                'product_id'        => null,
                'content'           => '[Contoh: "Anak saya memakai Scoliosis Brace dari SAM. Prosesnya cepat dan petugasnya sangat sabar menjelaskan cara pemakaian..."]',
                'rating'            => null,
                'photo'             => null,
                'consent_confirmed' => false,
                'is_active'         => false,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'A. W.',
                'role_or_info'      => 'Pengguna AFO',
                'product_id'        => null,
                'content'           => '[Contoh: "SAM membantu saya mendapatkan AFO yang pas. Pelayanannya ramah dan profesional..."]',
                'rating'            => null,
                'photo'             => null,
                'consent_confirmed' => false,
                'is_active'         => false,
                'sort_order'        => 3,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::firstOrCreate(
                ['name' => $data['name'], 'role_or_info' => $data['role_or_info']],
                $data
            );
        }
    }
}
