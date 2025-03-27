<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $tools = Category::create([
            'name_ru' => 'Инструменты',
            'name_ro' => 'Unelte',
            'name_en' => 'Tools',
            'parent_id' => null,
        ]);

        $materials = Category::create([
            'name_ru' => 'Стройматериалы',
            'name_ro' => 'Materiale',
            'name_en' => 'Materials',
            'parent_id' => null,
        ]);

        Category::create([
            'name_ru' => 'Электроинструменты',
            'name_ro' => 'Scule electrice',
            'name_en' => 'Power Tools',
            'parent_id' => $tools->id,
        ]);

        Category::create([
            'name_ru' => 'Ручной Инструмент',
            'name_ro' => 'Scule electrice',
            'name_en' => 'Power Tools',
            'parent_id' => $tools->id,
        ]);

        Category::create([
            'name_ru' => 'Фасады',
            'name_ro' => 'Materiale de construcție',
            'name_en' => 'Building Materials',
            'parent_id' => $materials->id,
        ]);

        Category::create([
            'name_ru' => 'Пиломатериалы',
            'name_ro' => 'Materiale de construcție',
            'name_en' => 'Building Materials',
            'parent_id' => $materials->id,
        ]);
    }
}
