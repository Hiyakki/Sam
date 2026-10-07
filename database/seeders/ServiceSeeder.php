<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Konsultasi',
                'slug' => 'konsultasi',
                'description' => 'Konsultasi langsung dengan tenaga ortotis prostétis SAM untuk penilaian kebutuhan alat bantu.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Maintenance Alat (Perbaikan)',
                'slug' => 'maintenance-alat-perbaikan',
                'description' => 'Servis dan perbaikan alat orthotic maupun prosthetic yang sudah ada.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Fitting',
                'slug' => 'fitting',
                'description' => 'Proses pengepasan dan penyesuaian alat orthotic atau prosthetic sesuai anatomi pasien.',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $data) {
            Service::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
