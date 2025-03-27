<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Description;

class DescriptionSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) {
            Description::insert([
                [
                    'product_id' => $product->id,
                    'language' => 'ru',
                    'title' => 'Товар ' . $product->id,
                    'short_description' => 'Краткое описание RU',
                    'full_description' => 'Полное описание товара на русском.',
                ],
                [
                    'product_id' => $product->id,
                    'language' => 'ro',
                    'title' => 'Produs ' . $product->id,
                    'short_description' => 'Descriere scurtă RO',
                    'full_description' => 'Descriere completă în română.',
                ],
                [
                    'product_id' => $product->id,
                    'language' => 'en',
                    'title' => 'Product ' . $product->id,
                    'short_description' => 'Short description EN',
                    'full_description' => 'Full product description in English.',
                ],
            ]);
        }
    }
}
