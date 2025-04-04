<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryTranslation;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $parentCategories = [
            'Инструменты', 'Стройматериалы', 'Отделка', 'Крепёж', 'Электрика',
            'Сантехника', 'Окна и двери', 'Изоляция', 'Лаки и краски', 'Сад и огород'
        ];

        foreach ($parentCategories as $ruName) {
            $category = Category::create(['parent_id' => null]);

            $this->createTranslations($category->id, $ruName);

            $subCount = rand(3, 5);
            for ($i = 1; $i <= $subCount; $i++) {
                $sub = Category::create(['parent_id' => $category->id]);

                $this->createTranslations(
                    $sub->id,
                    $ruName . ' - Подкатегория ' . $i,
                    'Subcategorie ' . $i,
                    'Subcategory ' . $i
                );
            }
        }
    }

    private function createTranslations($categoryId, $nameRu, $nameRo = null, $nameEn = null)
    {
        CategoryTranslation::insert([
            [
                'category_id' => $categoryId,
                'language' => 'ru',
                'name' => $nameRu,
            ],
            [
                'category_id' => $categoryId,
                'language' => 'ro',
                'name' => $nameRo ?? $this->translateToRo($nameRu),
            ],
            [
                'category_id' => $categoryId,
                'language' => 'en',
                'name' => $nameEn ?? $this->translateToEn($nameRu),
            ],
        ]);
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
