<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Service;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['slug' => 'sam-bogor'],
            [
                'name' => 'SAM Bogor',
                'address' => 'Jl. Contoh No. 1, Bogor, Jawa Barat [LENGKAPI]',
                'phone_wa' => '6281234567890',
                'email' => 'sam.bogor@example.com',
                'maps_embed_url' => null,
                'maps_link' => null,
                'photo' => null,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Hubungkan semua layanan ke cabang ini
        $serviceIds = Service::pluck('id')->toArray();
        $branch->services()->sync($serviceIds);
    }
}
