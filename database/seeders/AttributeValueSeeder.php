<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\AttributeValueTranslation;

class AttributeValueSeeder extends Seeder
{
    public function run()
    {
        $values = [
            'Мощность' => ['500 Вт', '1000 Вт'],
            'Цвет' => ['Красный', 'Синий'],
            'Длина' => ['1 м', '2 м'],
            'Ширина' => ['50 см', '100 см'],
            'Вес' => ['1 кг', '5 кг'],
        ];

        foreach ($values as $attrNameRu => $valSet) {
            $attribute = ProductAttribute::whereHas('translations', function ($query) use ($attrNameRu) {
                $query->where('language', 'ru')->where('name', $attrNameRu);
            })->first();

            if (!$attribute) continue;

            foreach ($valSet as $valRu) {
                $value = AttributeValue::create([
                    'attribute_id' => $attribute->id,
                ]);

                AttributeValueTranslation::insert([
                    [
                        'attribute_value_id' => $value->id,
                        'language' => 'ru',
                        'value' => $valRu,
                    ],
                    [
                        'attribute_value_id' => $value->id,
                        'language' => 'ro',
                        'value' => $this->translateToRo($valRu),
                    ],
                    [
                        'attribute_value_id' => $value->id,
                        'language' => 'en',
                        'value' => $this->translateToEn($valRu),
                    ],
                ]);
            }
        }
    }

    private function translateToRo($val)
    {
        return match ($val) {
            '500 Вт' => '500 W',
            '1000 Вт' => '1000 W',
            'Красный' => 'Roșu',
            'Синий' => 'Albastru',
            '1 м' => '1 m',
            '2 м' => '2 m',
            '50 см' => '50 cm',
            '100 см' => '100 cm',
            '1 кг' => '1 kg',
            '5 кг' => '5 kg',
            default => $val,
        };
    }

    private function translateToEn($val)
    {
        return match ($val) {
            '500 Вт' => '500 W',
            '1000 Вт' => '1000 W',
            'Красный' => 'Red',
            'Синий' => 'Blue',
            '1 м' => '1 m',
            '2 м' => '2 m',
            '50 см' => '50 cm',
            '100 см' => '100 cm',
            '1 кг' => '1 kg',
            '5 кг' => '5 kg',
            default => $val,
        };
    }
}
