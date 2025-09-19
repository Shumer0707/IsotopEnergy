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
        // Значения для термопанелей и декора
        $values = [
            'Толщина' => ['30 мм', '40 мм', '50 мм', '60 мм', '70 мм', '80 мм', '90 мм', '100 мм'],
            'Плотность' => ['60 eps', '80 eps', '100 eps'],
            'Ширина' => $this->generateSizeRange(20, 500, 5), // от 20 до 500 с шагом 5
            'Высота' => $this->generateSizeRange(20, 500, 5), // от 20 до 500 с шагом 5
            'Длина' => ['1200 мм', '2000 мм'],
        ];

        foreach ($values as $attrNameRu => $valSet) {
            // Ищем атрибут по русскому названию
            $attribute = ProductAttribute::whereHas('translations', function ($query) use ($attrNameRu) {
                $query->where('language', 'ru')->where('name', $attrNameRu);
            })->first();

            // Если атрибут не найден, пропускаем
            if (!$attribute) {
                echo "Атрибут '{$attrNameRu}' не найден\n";
                continue;
            }

            foreach ($valSet as $valRu) {
                // Создаем запись значения
                $value = AttributeValue::create([
                    'attribute_id' => $attribute->id,
                ]);

                // Добавляем переводы для всех языков
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

    /**
     * Генерирует массив размеров от min до max с шагом step
     */
    private function generateSizeRange($min, $max, $step)
    {
        $sizes = [];
        for ($i = $min; $i <= $max; $i += $step) {
            $sizes[] = $i . ' мм';
        }
        return $sizes;
    }

    /**
     * Переводы на румынский
     */
    private function translateToRo($val)
    {
        return match (true) {
            // Толщина - конкретные значения
            $val === '30 мм' => '30 mm',
            $val === '40 мм' => '40 mm',
            $val === '50 мм' => '50 mm',
            $val === '60 мм' => '60 mm',
            $val === '70 мм' => '70 mm',
            $val === '80 мм' => '80 mm',
            $val === '90 мм' => '90 mm',
            $val === '100 мм' => '100 mm',

            // Плотность
            $val === '60 eps' => '60 eps',
            $val === '80 eps' => '80 eps',
            $val === '100 eps' => '100 eps',

            // Длина
            $val === '1200 мм' => '1200 mm',
            $val === '2000 мм' => '2000 mm',

            // Для ширины и высоты - автоматическая замена мм на mm
            str_ends_with($val, ' мм') => str_replace(' мм', ' mm', $val),

            default => $val,
        };
    }

    /**
     * Переводы на английский
     */
    private function translateToEn($val)
    {
        return match (true) {
            // Толщина - конкретные значения
            $val === '30 мм' => '30 mm',
            $val === '40 мм' => '40 mm',
            $val === '50 мм' => '50 mm',
            $val === '60 мм' => '60 mm',
            $val === '70 мм' => '70 mm',
            $val === '80 мм' => '80 mm',
            $val === '90 мм' => '90 mm',
            $val === '100 мм' => '100 mm',

            // Плотность
            $val === '60 eps' => '60 eps',
            $val === '80 eps' => '80 eps',
            $val === '100 eps' => '100 eps',

            // Длина
            $val === '1200 мм' => '1200 mm',
            $val === '2000 мм' => '2000 mm',

            // Для ширины и высоты - автоматическая замена мм на mm
            str_ends_with($val, ' мм') => str_replace(' мм', ' mm', $val),

            default => $val,
        };
    }
}
