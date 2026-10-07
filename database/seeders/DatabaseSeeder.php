<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan penting: layanan dan cabang harus ada sebelum relasi/jadwal.
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,           // 1. Layanan (tidak bergantung apapun)
            BranchSeeder::class,            // 2. Cabang + relasi ke layanan
            BranchScheduleSeeder::class,    // 3. Jadwal cabang
            ProductCategorySeeder::class,   // 4. Kategori produk
            ProductSeeder::class,           // 5. Produk (butuh kategori)
            SiteSettingSeeder::class,       // 6. Pengaturan situs
            FaqSeeder::class,               // 7. FAQ
            TestimonialSeeder::class,       // 8. Testimoni contoh
        ]);
    }
}
