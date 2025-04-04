<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $parentCategories = [
            'Инструменты', 'Стройматериалы', 'Отделка', 'Крепёж', 'Электрика',
            'Сантехника', 'Окна и двери', 'Изоляция', 'Лаки и краски', 'Сад и огород'
        ];

        foreach ($parentCategories as $parentNameRu) {
            $parent = Category::create([
                'name_ru' => $parentNameRu,
                'name_ro' => $this->translateToRo($parentNameRu),
                'name_en' => $this->translateToEn($parentNameRu),
                'parent_id' => null,
            ]);

            $subCount = rand(3, 5);
            for ($i = 1; $i <= $subCount; $i++) {
                Category::create([
                    'name_ru' => $parentNameRu . ' - Подкатегория ' . $i,
                    'name_ro' => $this->translateToRo($parentNameRu) . ' - Subcategorie ' . $i,
                    'name_en' => $this->translateToEn($parentNameRu) . ' - Subcategory ' . $i,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }

    private function translateToRo($ru)
    {
        return match ($ru) {
            'Инструменты' => 'Unelte',
            'Стройматериалы' => 'Materiale de construcție',
            'Отделка' => 'Finisaje',
            'Крепёж' => 'Elemente de fixare',
            'Электрика' => 'Electricitate',
            'Сантехника' => 'Instalații sanitare',
            'Окна и двери' => 'Ferestre și uși',
            'Изоляция' => 'Izolație',
            'Лаки и краски' => 'Lacuri și vopsele',
            'Сад и огород' => 'Grădină și legume',
            default => $ru,
        };
    }

    private function translateToEn($ru)
    {
        return match ($ru) {
            'Инструменты' => 'Tools',
            'Стройматериалы' => 'Building Materials',
            'Отделка' => 'Finishing',
            'Крепёж' => 'Fasteners',
            'Электрика' => 'Electricity',
            'Сантехника' => 'Plumbing',
            'Окна и двери' => 'Windows and Doors',
            'Изоляция' => 'Insulation',
            'Лаки и краски' => 'Paints and Varnishes',
            'Сад и огород' => 'Garden',
            default => $ru,
        };
    }
}
