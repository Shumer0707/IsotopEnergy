<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        $power = ProductAttribute::create([
            'name_ru' => 'Мощность',
            'name_ro' => 'Putere',
            'name_en' => 'Power',
        ]);

        $color = ProductAttribute::create([
            'name_ru' => 'Цвет',
            'name_ro' => 'Culoare',
            'name_en' => 'Color',
        ]);

        AttributeValue::insert([
            // для Мощность
            ['attribute_id' => $power->id, 'value_ru' => '500 Вт', 'value_ro' => '500 W', 'value_en' => '500 W'],
            ['attribute_id' => $power->id, 'value_ru' => '1000 Вт', 'value_ro' => '1000 W', 'value_en' => '1000 W'],

            // для Цвет
            ['attribute_id' => $color->id, 'value_ru' => 'Красный', 'value_ro' => 'Roșu', 'value_en' => 'Red'],
            ['attribute_id' => $color->id, 'value_ru' => 'Синий', 'value_ro' => 'Albastru', 'value_en' => 'Blue'],
        ]);
    }
}
