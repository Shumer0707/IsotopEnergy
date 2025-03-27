<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run()
    {
        Brand::create([
            'name' => 'Makita',
            'logo' => 'brands/makita.png',
        ]);

        Brand::create([
            'name' => 'Bosch',
            'logo' => 'brands/bosch.png',
        ]);

        Brand::create([
            'name' => 'Knauf',
            'logo' => 'brands/knauf.png',
        ]);
    }
}
