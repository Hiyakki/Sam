<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUpperLimbOrthosis();
        $this->seedLowerLimbOrthosis();
        $this->seedSpinalOrthosis();
        $this->seedUpperLimbProsthesis();
        $this->seedLowerLimbProsthesis();
        $this->seedKomponen();
    }

    private function create(string $categorySlug, array $products): void
    {
        $category = ProductCategory::where('slug', $categorySlug)->firstOrFail();

        foreach ($products as $i => $data) {
            $slug = Str::slug($data['name']);
            // Pastikan slug unik bila ada nama yang sama
            $existing = Product::where('slug', 'like', $slug.'%')
                ->whereNot('id', 0)->count();
            if ($existing > 0) {
                $slug = $slug.'-'.($existing + 1);
            }

            Product::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'] ?? null,
                    'function' => $data['function'] ?? null,
                    'indication' => $data['indication'] ?? null,
                    'photo' => null,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                    'price' => null,
                ]
            );
        }
    }

    private function seedUpperLimbOrthosis(): void
    {
        $this->create('upper-limb-orthosis', [
            [
                'name' => 'Wrist Thumb Support',
                'function' => 'Fiksasi',
                'indication' => 'Rheumatic Arthritis, Trigger Finger',
            ],
            [
                'name' => 'Wrist Support (Tipe 1)',
                'function' => 'Fiksasi',
                'indication' => 'CTS ringan, Trauma pergelangan tangan, Tendinitis pergelangan tangan',
            ],
            [
                'name' => 'Wrist Support (Tipe 2)',
                'function' => 'Fiksasi',
                'indication' => 'CTS, Trauma pergelangan tangan, Tendinitis pergelangan tangan',
            ],
            [
                'name' => 'Thumb Support',
                'function' => 'Fiksasi',
                'indication' => 'Rheumatic Arthritis, Trigger Finger',
            ],
            [
                'name' => 'Finger Splint',
                'function' => 'Fiksasi dan Koreksi',
                'indication' => 'Trigger Finger',
            ],
            [
                'name' => 'Dynamic Cock Up Splint',
                'function' => 'Fiksasi dan Koreksi',
                'indication' => 'Post OP fraktur radius atau ulna, Nerve Injury, Drop hand',
            ],
            [
                'name' => 'Resting Splint',
                'function' => 'Fiksasi, Koreksi',
                'indication' => 'Drop hand, Post Stroke, CP, Claw hand, Post OP',
            ],
            [
                'name' => 'Axillary Support',
                'function' => 'Fiksasi',
                'indication' => 'Cedera shoulder',
            ],
            [
                'name' => 'Shoulder Support',
                'function' => 'Fiksasi',
                'indication' => 'Cedera Acromio Clavicular joint',
            ],
        ]);
    }

    private function seedLowerLimbOrthosis(): void
    {
        $this->create('lower-limb-orthosis', [
            [
                'name' => 'Insole',
                'function' => 'Fiksasi dan Koreksi',
                'indication' => 'Flatfoot, Intoeing',
            ],
            [
                'name' => 'Dennis Brown Splint (DBS)',
                'function' => 'Koreksi dan Fiksasi',
                'indication' => 'CTEV',
            ],
            [
                'name' => 'SMO Splint',
                'function' => 'Koreksi',
                'indication' => 'Ankle tidak stabil, bagian tengah kaki tidak lurus, kaki bengkok ke dalam atau keluar',
            ],
            [
                'name' => 'Orthopedic Shoes',
                'function' => 'Koreksi',
                'indication' => 'Flatfoot, LLD, Intoeing & Toeingout, Metatarsus Adductus',
            ],
            [
                'name' => 'Diabetic Shoes',
                'function' => 'Fiksasi',
                'indication' => 'Diabetes mellitus',
            ],
            [
                'name' => 'AFO',
                'function' => 'Fiksasi, Koreksi',
                'indication' => 'CP, CTEV, LLD, Polio, Drop Foot, Pes Valgus, Pes Varus',
            ],
            [
                'name' => 'PTB Brace',
                'function' => 'Fiksasi, Koreksi',
                'indication' => 'Charcot Joint, Pembengkakan, Fraktur',
            ],
            [
                'name' => 'AFO with Rocker Bottom',
                'function' => 'Fiksasi, Koreksi',
                'indication' => 'Nyeri, sensitif saat berjalan [LENGKAPI: indikasi terpotong]',
            ],
            [
                'name' => 'KAFO',
                'function' => 'Koreksi dan Kompensasi',
                'indication' => 'CP, Genu Valgus, Genu Varus, Post Poliomyelitis, Atrofi otot',
            ],
            [
                'name' => 'HKAFO',
                'function' => 'Fiksasi dan Mobilisasi',
                'indication' => 'Paraplegia, Spina Bifida, CP, Dislokasi panggul',
            ],
            [
                'name' => 'Knee Brace (Tipe 1)',
                'function' => 'Fiksasi dan Koreksi',
                'indication' => 'Genu Valgus, Genu Varus, Hiperekstensi/hiperfleksi Knee, Knee Laxity',
            ],
            [
                'name' => 'Knee Brace (Tipe 2)',
                'function' => 'Fiksasi dan Mobilisasi',
                'indication' => 'ACL, kaki O/X, Limitasi pergerakan lutut grade 2, 3, dan 4',
            ],
            [
                'name' => 'Hip Stabilizer',
                'function' => 'Fiksasi',
                'indication' => 'Limitasi pergerakan hip joint',
            ],
            [
                'name' => 'Ankle Support',
                'function' => 'Fiksasi dan Mobilisasi',
                'indication' => 'Cedera pergelangan kaki',
            ],
        ]);
    }

    private function seedSpinalOrthosis(): void
    {
        $this->create('spinal-orthosis', [
            [
                'name' => 'Soft Collar Neck',
                'function' => 'Fiksasi',
                'indication' => 'CP, Trauma leher, Fraktur Cervical (cedera ringan)',
            ],
            [
                'name' => 'Semi Rigid Collar (Rigid Collar Neck)',
                'function' => 'Fiksasi',
                'indication' => 'CP, Trauma leher, Fraktur Cervical',
            ],
            [
                'name' => 'Collar Brace',
                'function' => 'Fiksasi',
                'indication' => 'CP, Trauma leher, Fraktur Cervical (cedera berat)',
            ],
            [
                'name' => 'LS Corset',
                'function' => 'Fiksasi',
                'indication' => 'Cedera otot area lumbal, HNP, Spondylosis, LBP, Arthritis',
            ],
            [
                'name' => 'LSO Brace',
                'function' => 'Fiksasi',
                'indication' => 'Fraktur, LBP',
            ],
            [
                'name' => 'LSO Fabric',
                'function' => 'Fiksasi',
                'indication' => 'Fraktur, LBP',
            ],
            [
                'name' => 'TLSK',
                'function' => 'Fiksasi',
                'indication' => 'Nyeri otot, HNP, LBP',
            ],
            [
                'name' => 'TLSK Fabric',
                'function' => 'Fiksasi',
                'indication' => 'Nyeri otot, HNP, LBP',
            ],
            [
                'name' => 'TLSO',
                'function' => 'Fiksasi dan Koreksi',
                'indication' => 'HNP, LBP, Post Op, Fraktur',
            ],
            [
                'name' => 'Jewett Brace',
                'function' => 'Fiksasi',
                'indication' => 'Fraktur, Kifosis [LENGKAPI: indikasi terpotong]',
            ],
            [
                'name' => 'CTO Brace',
                'function' => 'Fiksasi',
                'indication' => 'Fraktur, Spinal Injury, Whiplash Injury',
            ],
            [
                'name' => 'Scoliosis Brace (Milwaukee)',
                'function' => 'Koreksi derajat skoliosis',
                'indication' => 'Scoliosis',
            ],
            [
                'name' => 'Scoliosis Brace (MSO)',
                'function' => 'Koreksi derajat skoliosis',
                'indication' => 'Scoliosis',
            ],
            [
                'name' => 'Scoliosis Brace (Asymmetric MSO)',
                'function' => 'Koreksi derajat skoliosis',
                'indication' => 'Scoliosis',
            ],
            [
                'name' => 'Scoliosis Brace (DMSO)',
                'function' => 'Koreksi derajat skoliosis',
                'indication' => 'Scoliosis',
            ],
        ]);
    }

    private function seedUpperLimbProsthesis(): void
    {
        $this->create('upper-limb-prosthesis', [
            [
                'name' => 'Finger Prosthesis',
                'indication' => 'Level amputasi pada jari-jari tangan',
            ],
            [
                'name' => 'Hand Gloves Prosthesis',
                'indication' => 'Level amputasi bawah pergelangan tangan (telapak sampai jari tangan)',
            ],
            [
                'name' => 'AE Prosthesis',
                'indication' => 'Amputasi di bawah sendi bahu',
            ],
            [
                'name' => 'BE Prosthesis',
                'indication' => 'Amputasi di bawah sendi siku',
            ],
            [
                'name' => 'BBE Prosthesis',
                'indication' => 'Amputasi di bawah sendi siku [LENGKAPI: mohon dikonfirmasi perbedaan dengan BE]',
            ],
        ]);
    }

    private function seedLowerLimbProsthesis(): void
    {
        $this->create('lower-limb-prosthesis', [
            [
                'name' => 'Foot Prosthesis',
                'indication' => 'Level amputasi bawah pergelangan kaki sampai jari-jari kaki',
            ],
            [
                'name' => 'Transtibial Prosthesis',
                'indication' => 'Level amputasi bawah lutut / tulang tibia',
            ],
            [
                'name' => 'Syme Prosthesis',
                'indication' => 'Level amputasi tulang tibia dan ankle',
            ],
            [
                'name' => 'Transfemoral Prosthesis',
                'indication' => 'Level amputasi bawah pinggang / tulang femur',
            ],
            [
                'name' => 'Hip Disarticulation Prosthesis',
                'indication' => 'Level amputasi tepat pada tulang hip',
            ],
        ]);
    }

    private function seedKomponen(): void
    {
        // Socket
        $this->create('socket', [
            ['name' => 'Above Knee Socket',        'description' => '[LENGKAPI]'],
            ['name' => 'Below Knee Socket',         'description' => '[LENGKAPI]'],
            ['name' => 'Soft Socket',               'description' => '[LENGKAPI]'],
            ['name' => 'Silicone Liner - Cushion',  'description' => '[LENGKAPI]'],
            ['name' => 'Silicone Liner - Locking',  'description' => '[LENGKAPI]'],
        ]);

        // Adaptor Socket
        $this->create('adaptor-socket', [
            ['name' => 'Adaptor Socket Tipe 1', 'description' => '[LENGKAPI]'],
            ['name' => 'Adaptor Socket Tipe 2', 'description' => '[LENGKAPI]'],
            ['name' => 'Adaptor Socket Tipe 3', 'description' => '[LENGKAPI]'],
            ['name' => 'Adaptor Socket Tipe 4', 'description' => '[LENGKAPI]'],
        ]);

        // Tube & Klem
        $this->create('tube-klem', [
            ['name' => 'Tube',  'description' => '[LENGKAPI]'],
            ['name' => 'Klem',  'description' => '[LENGKAPI]'],
        ]);

        // Knee Joint
        $this->create('knee-joint', [
            ['name' => 'Mechanical Knee Axis',    'description' => '[LENGKAPI]'],
            ['name' => 'Pneumatic Knee Joint (Varian 1)', 'description' => '[LENGKAPI]'],
            ['name' => 'Pneumatic Knee Joint (Varian 2)', 'description' => '[LENGKAPI]'],
            ['name' => 'Hydraulic Knee Joint',    'description' => '[LENGKAPI]'],
            ['name' => 'C-Leg',                   'description' => '[LENGKAPI]'],
        ]);

        // Foot Component
        $this->create('foot-component', [
            ['name' => 'SACH Foot',         'description' => '[LENGKAPI]'],
            ['name' => 'Single Axis Foot',  'description' => '[LENGKAPI]'],
            ['name' => 'Multi Axis Foot',   'description' => '[LENGKAPI]'],
            ['name' => 'Foot Cover',        'description' => '[LENGKAPI]'],
        ]);

        // Foot Component (Sport)
        $this->create('foot-component-sport', [
            ['name' => 'E-PRF01', 'description' => '[LENGKAPI]'],
            ['name' => 'E-PRF02', 'description' => '[LENGKAPI]'],
            ['name' => 'E-PRF03', 'description' => '[LENGKAPI]'],
            ['name' => 'E-PRF04', 'description' => '[LENGKAPI]'],
            ['name' => 'E-PRF05', 'description' => '[LENGKAPI]'],
            ['name' => 'E-PRF06', 'description' => '[LENGKAPI]'],
        ]);
    }
}
