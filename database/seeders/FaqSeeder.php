<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara membuat janji di SAM Orthotic Prosthetic?',
                'answer' => 'Anda dapat membuat janji melalui halaman <a href="/appointment">Appointment</a> di website ini. Pilih kantor, layanan, tanggal, dan jam yang tersedia, lalu isi data diri dan keluhan Anda. Setelah submit, pendaftaran Anda akan diverifikasi oleh admin kami dan Anda akan menerima konfirmasi melalui WhatsApp atau email.',
                'category' => 'Appointment',
                'is_active' => true,
                'show_on_home' => true,
                'sort_order' => 1,
            ],
            [
                'question' => 'Berapa kali kunjungan yang dibutuhkan untuk alat custom?',
                'answer' => 'Untuk alat custom (misalnya kaki palsu atau brace khusus), umumnya dibutuhkan minimal 2–3 kunjungan: konsultasi & pengukuran, fitting (pengepasan), dan kontrol. Jumlah kunjungan dapat berbeda tergantung kebutuhan dan kondisi pasien.',
                'category' => 'Umum',
                'is_active' => true,
                'show_on_home' => true,
                'sort_order' => 2,
            ],
            [
                'question' => 'Apakah perlu membawa resep atau surat rujukan dokter?',
                'answer' => 'Resep atau surat rujukan dokter <strong>disarankan</strong> namun tidak wajib untuk konsultasi pertama. Untuk pembuatan alat tertentu (terutama prosthetic), resep dokter ortopedi atau rehabilitasi medik akan sangat membantu proses pembuatan alat yang tepat.',
                'category' => 'Umum',
                'is_active' => true,
                'show_on_home' => false,
                'sort_order' => 3,
            ],
            [
                'question' => 'Bagaimana cara merawat alat orthotic atau prosthetic saya?',
                'answer' => '<ul><li>Bersihkan alat secara rutin dengan kain lembap.</li><li>Hindari perendaman dalam air.</li><li>Simpan di tempat yang sejuk dan kering, jauh dari paparan matahari langsung.</li><li>Segera hubungi kami jika ada kerusakan atau rasa tidak nyaman.</li></ul>',
                'category' => 'Produk',
                'is_active' => true,
                'show_on_home' => false,
                'sort_order' => 4,
            ],
            [
                'question' => 'Bagaimana cara mengubah atau membatalkan janji?',
                'answer' => 'Untuk perubahan jadwal atau pembatalan, silakan hubungi admin cabang kami melalui nomor WhatsApp atau telepon yang tercantum pada kontak kami. Perubahan jadwal dan pembatalan diproses langsung oleh tim admin kami.',
                'category' => 'Appointment',
                'is_active' => true,
                'show_on_home' => true,
                'sort_order' => 5,
            ],
            [
                'question' => 'Di mana saja kantor SAM Orthotic Prosthetic?',
                'answer' => 'Informasi lengkap lokasi, alamat, dan jam operasional tersedia di halaman <a href="/about">About Us</a>. Anda juga dapat menghubungi kami melalui WhatsApp untuk informasi lebih lanjut.',
                'category' => 'Umum',
                'is_active' => true,
                'show_on_home' => false,
                'sort_order' => 6,
            ],
            [
                'question' => 'Berapa lama proses pembuatan alat prosthetic?',
                'answer' => 'Lama pembuatan bervariasi tergantung jenis alat. Untuk alat sederhana dapat selesai dalam beberapa hari, sedangkan prosthetic custom dapat memerlukan 1–3 minggu. Tim kami akan memberikan estimasi waktu yang lebih akurat setelah konsultasi.',
                'category' => 'Produk',
                'is_active' => true,
                'show_on_home' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($faqs as $data) {
            Faq::firstOrCreate(
                ['question' => $data['question']],
                $data
            );
        }
    }
}
