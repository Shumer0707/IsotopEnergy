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
      'Толщина' => ['Grosime', 'Thickness'],
      'Плотность' => ['Densitate', 'Density'],
      'Ширина' => ['Lățime', 'Width'],
      'Высота' => ['Înălțime', 'Height'],
      'Длина' => ['Lungime', 'Length'],
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
