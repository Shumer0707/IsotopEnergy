<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\ProductAttributeValue;

class ProductAttributeValueSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $attributes = ProductAttribute::all();

        foreach ($products as $product) {
            foreach ($attributes as $attribute) {
                $values = $attribute->values;

                if ($values->isEmpty()) {
                    continue;
                }

                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                    'attribute_value_id' => $values->random()->id,
                ]);
            }
        }
    }
}
