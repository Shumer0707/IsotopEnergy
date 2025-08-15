<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryTranslation;

class CategorySeeder extends Seeder
{
  public function run()
  {
    // Основные категории и подкатегории
    $categories = [
      'Термопанели' => [
        'Термопанели UKR',
        'Термопанели POL',
        'Термопанели POL Cerad',
      ],
      'Декоративные элементы' => [
        'Молдинги',
        'Карнизы',
        'Капитель',
        'База',
        'Капельник',
        'Пилястра',
        'Колона',
        'Декор карниза',
      ],
    ];

    foreach ($categories as $parentName => $subcategories) {
      $parent = Category::create(['parent_id' => null]);
      $this->createTranslations($parent->id, $parentName);

      foreach ($subcategories as $subName) {
        $sub = Category::create(['parent_id' => $parent->id]);
        $this->createTranslations($sub->id, $subName);
      }
    }
  }

  private function createTranslations($categoryId, $nameRu)
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
        'name' => $this->translateToRo($nameRu),
      ],
      [
        'category_id' => $categoryId,
        'language' => 'en',
        'name' => $this->translateToEn($nameRu),
      ],
    ]);
  }

  private function translateToRo($ru)
  {
    return match ($ru) {
      'Термопанели' => 'Panouri termice',
      'Термопанели UKR' => 'Panouri termice UKR',
      'Термопанели POL' => 'Panouri termice POL',
      'Термопанели POL Cerad' => 'Panouri termice POL Cerad',
      'Декоративные элементы' => 'Elemente decorative',
      'Молдинги' => 'Profile decorative',
      'Карнизы' => 'Cornise',
      'Капитель' => 'Capitel',
      'База' => 'Bază',
      'Капельник' => 'Picurător',
      'Пилястра' => 'Pilastru',
      'Колона' => 'Coloană',
      'Декор карниза' => 'Decor de cornișă',
      default => $ru,
    };
  }

  private function translateToEn($ru)
  {
    return match ($ru) {
      'Термопанели' => 'Thermal Panels',
      'Термопанели UKR' => 'Thermal Panels UKR',
      'Термопанели POL' => 'Thermal Panels POL',
      'Термопанели POL Cerad' => 'Thermal Panels POL Cerad',
      'Декоративные элементы' => 'Decorative Elements',
      'Молдинги' => 'Moldings',
      'Карнизы' => 'Cornices',
      'Капитель' => 'Capital',
      'База' => 'Base',
      'Капельник' => 'Drip Cap',
      'Пилястра' => 'Pilaster',
      'Колона' => 'Column',
      'Декор карниза' => 'Cornice Decor',
      default => $ru,
    };
  }
}
