<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            [
                'name' => 'Orthosis',
                'slug' => 'orthosis',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Upper Limb Orthosis', 'slug' => 'upper-limb-orthosis', 'sort_order' => 1],
                    ['name' => 'Lower Limb Orthosis', 'slug' => 'lower-limb-orthosis', 'sort_order' => 2],
                    ['name' => 'Spinal Orthosis',     'slug' => 'spinal-orthosis',     'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Prosthesis',
                'slug' => 'prosthesis',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Upper Limb Prosthesis', 'slug' => 'upper-limb-prosthesis', 'sort_order' => 1],
                    ['name' => 'Lower Limb Prosthesis', 'slug' => 'lower-limb-prosthesis', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Komponen',
                'slug' => 'komponen',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Socket',               'slug' => 'socket',               'sort_order' => 1],
                    ['name' => 'Adaptor Socket',        'slug' => 'adaptor-socket',       'sort_order' => 2],
                    ['name' => 'Tube & Klem',           'slug' => 'tube-klem',            'sort_order' => 3],
                    ['name' => 'Knee Joint',            'slug' => 'knee-joint',           'sort_order' => 4],
                    ['name' => 'Foot Component',        'slug' => 'foot-component',       'sort_order' => 5],
                    ['name' => 'Foot Component (Sport)', 'slug' => 'foot-component-sport', 'sort_order' => 6],
                ],
            ],
        ];

        foreach ($tree as $rootData) {
            $children = $rootData['children'] ?? [];
            unset($rootData['children']);

            $root = ProductCategory::firstOrCreate(
                ['slug' => $rootData['slug']],
                array_merge($rootData, ['parent_id' => null])
            );

            foreach ($children as $childData) {
                ProductCategory::firstOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, ['parent_id' => $root->id])
                );
            }
        }
    }
}
