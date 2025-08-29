<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\Description;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;

class ProductService
{
  public function getIndexData(?int $categoryId = null): array
  {
    $productsQuery = Product::with(['category.translation', 'category', 'descriptions', 'brand']);

    if ($categoryId) {
      $productsQuery->where('category_id', $categoryId);
    }

    $products = $productsQuery->get()->map(function ($product) {
      $product->attributeValues = $product->attributeValues()->get();
      $product->images = $product->images()->get();
      return $product;
    });

    return [
      'products' => $products,
      'categories' => Category::with('translation')->get(),
      'brands' => Brand::all(),
      'attributes' => ProductAttribute::all(),
      'values' => AttributeValue::all(),
    ];
  }

  public function store(array $data): Product
  {
    return DB::transaction(function () use ($data) {
      // 1. Создаём товар
      $product = Product::create([
        'category_id' => $data['category_id'],
        'brand_id' => $data['brand_id'] ?? null,
        'price' => $data['price'],
        'discount_price' => $data['discount_price'] ?? null,
        'currency' => $data['currency'],
        'main_image' => null,
      ]);

      // 2. Создаём описания
      foreach (['ru', 'ro', 'en'] as $lang) {
        Description::create([
          'product_id' => $product->id,
          'language' => $lang,
          'title' => trim($data['descriptions'][$lang]['title']),
          'short_description' => trim($data['descriptions'][$lang]['short_description']),
          'full_description' => trim($data['descriptions'][$lang]['full_description'] ?? ''),
        ]);
      }

      // 3. Привязываем атрибуты
      foreach ($data['attributes'] ?? [] as $attr) {
        ProductAttributeValue::create([
          'product_id' => $product->id,
          'attribute_id' => $attr['attribute_id'],
          'attribute_value_id' => $attr['value_id'],
        ]);
      }

      return $product;
    });
  }

  public function update(Product $product, array $data): Product
  {
    return DB::transaction(function () use ($product, $data) {
      // 1. Обновляем сам товар
      $product->update([
        'category_id' => $data['category_id'],
        'brand_id' => $data['brand_id'] ?? null,
        'price' => $data['price'],
        'discount_price' => $data['discount_price'] ?? null,
        'currency' => $data['currency'],
      ]);

      // 2. Обновляем описания
      foreach (['ru', 'ro', 'en'] as $lang) {
        $product->descriptions()->updateOrCreate(
          ['language' => $lang],
          [
            'title' => trim($data['descriptions'][$lang]['title']),
            'short_description' => trim($data['descriptions'][$lang]['short_description']),
            'full_description' => trim($data['descriptions'][$lang]['full_description'] ?? ''),
          ]
        );
      }

      // 3. Обновляем атрибуты
      $product->attributeValues()->delete();

      foreach ($data['attributes'] ?? [] as $attr) {
        ProductAttributeValue::create([
          'product_id' => $product->id,
          'attribute_id' => $attr['attribute_id'],
          'attribute_value_id' => $attr['value_id'],
        ]);
      }

      return $product;
    });
  }

  public function destroy($product)
  {
    return $product->delete();
  }

  /**
   * Создание вариаций товара
   *
   * @param array $baseData - данные базового товара
   * @param array $variationConfig - конфиг вариаций
   * @param array $variationImages - изображения для вариаций (optional)
   * @return array - массив созданных товаров
   */
  public function createVariations(array $baseData, array $variationConfig, array $variationImages = []): array
  {
    return DB::transaction(function () use ($baseData, $variationConfig, $variationImages) {
      $createdProducts = [];

      // Получаем все комбинации атрибутов
      $combinations = $this->generateAttributeCombinations($variationConfig['variation_attributes']);

      foreach ($combinations as $combination) {
        // Пропускаем если админ не выбрал эту комбинацию
        if (!in_array($combination['key'], $variationConfig['selected_combinations'])) {
          continue;
        }

        // Создаем товар-вариацию
        $productData = $baseData;

        // Устанавливаем цену для этой вариации
        $productData['price'] = $variationConfig['prices'][$combination['key']] ?? $baseData['price'];

        // Создаем товар
        $product = Product::create([
          'category_id' => $productData['category_id'],
          'brand_id' => $productData['brand_id'] ?? null,
          'price' => $productData['price'],
          'discount_price' => $productData['discount_price'] ?? null,
          'currency' => $productData['currency'],
          'main_image' => null,
        ]);

        // Создаем описания с модифицированным названием
        foreach (['ru', 'ro', 'en'] as $lang) {
          $title = $productData['descriptions'][$lang]['title'];
          $variationTitle = $this->generateVariationTitle($title, $combination['attributes'], $lang);

          Description::create([
            'product_id' => $product->id,
            'language' => $lang,
            'title' => $variationTitle,
            'short_description' => $productData['descriptions'][$lang]['short_description'],
            'full_description' => $productData['descriptions'][$lang]['full_description'] ?? '',
          ]);
        }

        // Привязываем атрибуты вариации
        foreach ($combination['attributes'] as $attrId => $valueId) {
          ProductAttributeValue::create([
            'product_id' => $product->id,
            'attribute_id' => $attrId,
            'attribute_value_id' => $valueId,
          ]);
        }

        // Привязываем остальные атрибуты (если есть)
        foreach ($productData['attributes'] ?? [] as $attr) {
          // Не дублируем атрибуты вариации
          if (!array_key_exists($attr['attribute_id'], $combination['attributes'])) {
            ProductAttributeValue::create([
              'product_id' => $product->id,
              'attribute_id' => $attr['attribute_id'],
              'attribute_value_id' => $attr['value_id'],
            ]);
          }
        }

        $createdProducts[] = $product;

        // Загружаем изображения для этой вариации если есть
        // Загружаем изображения для этой вариации если есть
        if (isset($variationImages[$combination['key']]) && !empty($variationImages[$combination['key']])) {

          $imageService = app(\App\Services\ImageService::class);
          $imageService->upload($product, $variationImages[$combination['key']]);

          // Устанавливаем первое изображение как главное
          $firstImage = $product->images()->first();
          if ($firstImage) {
            $product->update(['main_image' => $firstImage->path]);
          }
        }
      }

      return $createdProducts;
    });
  }

  /**
   * Генерация всех комбинаций атрибутов
   */
  private function generateAttributeCombinations(array $attributes): array
  {
    $combinations = [];
    $attributeIds = array_keys($attributes);

    // Рекурсивно генерируем все комбинации
    $this->generateCombinationsRecursive($attributes, $attributeIds, [], $combinations);

    return $combinations;
  }

  /**
   * Рекурсивная генерация комбинаций
   */
  private function generateCombinationsRecursive(array $attributes, array $attributeIds, array $current, array &$combinations, int $index = 0)
  {
    if ($index >= count($attributeIds)) {
      // Создаем ключ в том же формате что и во фронтенде: "1_5_2_8"
      $keyParts = [];
      foreach ($attributeIds as $attrId) {
        $keyParts[] = $attrId . '_' . $current[$attrId];
      }
      $key = implode('_', $keyParts);

      $combinations[] = [
        'key' => $key,
        'attributes' => $current
      ];
      return;
    }

    $attributeId = $attributeIds[$index];
    $valueIds = $attributes[$attributeId];

    foreach ($valueIds as $valueId) {
      $current[$attributeId] = $valueId;
      $this->generateCombinationsRecursive($attributes, $attributeIds, $current, $combinations, $index + 1);
    }
  }

  /**
   * Генерация названия с атрибутами
   */
  private function generateVariationTitle(string $baseTitle, array $attributes, string $language): string
  {
    // Получаем значения атрибутов для названия
    $attributeValues = [];

    foreach ($attributes as $attrId => $valueId) {
      $value = \App\Models\AttributeValue::find($valueId);
      if ($value) {
        $translation = $value->translations()->where('language', $language)->first();
        if ($translation) {
          $attributeValues[] = $translation->value;
        }
      }
    }

    // Формируем название: "Базовое название (атрибут1, атрибут2)"
    if (!empty($attributeValues)) {
      return $baseTitle . ' (' . implode(', ', $attributeValues) . ')';
    }

    return $baseTitle;
  }
}
