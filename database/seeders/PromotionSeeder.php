<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\DiscountGroup;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $products = Product::inRandomOrder()->take(20)->get();
        $discountGroups = DiscountGroup::all();

        foreach ($products as $product) {
            Promotion::create([
                'product_id' => $product->id,
                'discount_group_id' => $discountGroups->random()->id,
                'starts_at' => now(),
                'ends_at' => now()->addWeeks(2),
                'active' => true,
            ]);
        }
    }
}
