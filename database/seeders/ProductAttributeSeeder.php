<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\AttributeTranslation;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        $attributes = [
            'Мощность' => ['Putere', 'Power'],
            'Цвет' => ['Culoare', 'Color'],
            'Длина' => ['Lungime', 'Length'],
            'Ширина' => ['Lățime', 'Width'],
            'Вес' => ['Greutate', 'Weight'],
        ];

        foreach ($attributes as $ru => [$ro, $en]) {
            $attribute = ProductAttribute::create();

            AttributeTranslation::insert([
                [
                    'product_attribute_id' => $attribute->id,
                    'language' => 'ru',
                    'name' => $ru,
                ],
                [
                    'product_attribute_id' => $attribute->id,
                    'language' => 'ro',
                    'name' => $ro,
                ],
                [
                    'product_attribute_id' => $attribute->id,
                    'language' => 'en',
                    'name' => $en,
                ],
            ]);
        }
    }
}
