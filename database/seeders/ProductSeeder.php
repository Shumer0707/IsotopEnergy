<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        $brands = Brand::all();

        for ($i = 1; $i <= 200; $i++) {
            Product::create([
                'category_id' => $categories->whereNotNull('parent_id')->random()->id,
                'brand_id' => $brands->random()->id,
                'price' => rand(500, 5000),
                'discount_price' => rand(400, 4900),
                'currency' => 'MDL',
                'main_image' => NULL,
            ]);
        }
    }
}
