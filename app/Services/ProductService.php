<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;
use App\Models\Description;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use Illuminate\Support\Facades\DB;

class ProductService
{
  public function getIndexData(?int $categoryId = null): array
  {
    $productsQuery = Product::with(['category.translation', 'category', 'descriptions', 'brand']);

    if ($categoryId) {
      $productsQuery->where('category_id', $categoryId);
    }

    $products = $productsQuery->paginate(30);

    $products->getCollection()->transform(function ($product) {
      // Загружаем варианты с атрибутами
      $product->variants = $product->variants()->with(['variantAttributes.attribute.translations', 'variantAttributes.attributeValue.translations'])->get();
      $product->images = $product->images()->get();

      // Для совместимости с таблицей - цена дефолтного варианта
      $defaultVariant = $product->variants->where('is_default', true)->first();
      $product->price = $defaultVariant ? $defaultVariant->price : 0;

      // ✅ ДОБАВЛЯЕМ: Подготавливаем варианты для формы редактирования
      $product->variantsForEdit = $product->variants->map(function ($variant) {
        return [
          'id' => $variant->id,
          'sku' => $variant->sku,
          'price' => $variant->price,
          'is_default' => $variant->is_default,
          'attributes' => $variant->variantAttributes->map(function ($va) {
            return [
              'attribute_id' => $va->attribute_id,
              'attribute_name' => $va->attribute->translatedName(),
              'value_id' => $va->attribute_value_id,
              'value_name' => $va->attributeValue->translatedValue(),
            ];
          }),
        ];
      });

      return $product;
    });

    return [
      'products' => $products,
      'categories' => Category::with('translation')->get(),
      'brands' => Brand::all(),
      'attributes' => ProductAttribute::with('translations')->get(),
      'values' => AttributeValue::with('translations')->get(),
    ];
  }

  public function store(array $data): Product
  {
    return DB::transaction(function () use ($data) {
      // 1. Создаём основной товар (БЕЗ цены)
      $product = Product::create([
        'category_id' => $data['category_id'],
        'brand_id' => $data['brand_id'] ?? null,
        'base_sku' => $data['base_sku'] ?? null,
        'currency' => $data['currency'],
        'main_image' => null,
        'measurement' => $data['measurement'],
      ]);

      if (empty($product->base_sku)) {
        $product->update(['base_sku' => (string)$product->id]);
      }

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

      // 3. Обрабатываем изображения
      if (!empty($data['images'])) {
        $imageService = app(\App\Services\ImageService::class);
        $imageService->upload($product, $data['images']);

        // Устанавливаем первое изображение как главное
        $firstImage = $product->images()->first();
        if ($firstImage) {
          $product->update(['main_image' => $firstImage->path]);
        }
      }

      // 4. Создаём вариант(ы)
      if (isset($data['create_variations']) && $data['create_variations']) {
        // Создаём множественные варианты
        $this->createMultipleVariants($product, $data);
      } else {
        // Создаём один простой вариант
        $this->createSingleVariant($product, $data);
      }

      return $product;
    });
  }

  /**
   * Создание одного простого варианта (для обычных товаров)
   */
  private function createSingleVariant(Product $product, array $data): void
  {
    // Создаём один вариант с базовой ценой
    $variant = ProductVariant::create([
      'product_id' => $product->id,
      'sku' => $this->generateSingleVariantSku($product),
      'price' => $data['price'],
      'is_default' => true, // Единственный вариант автоматически дефолтный
    ]);

    // Привязываем обычные атрибуты к варианту
    foreach ($data['attributes'] ?? [] as $attr) {
      ProductVariantAttribute::create([
        'variant_id' => $variant->id,
        'attribute_id' => $attr['attribute_id'],
        'attribute_value_id' => $attr['value_id'],
      ]);
    }

    // Обновляем SKU с учетом атрибутов
    $variant->update(['sku' => $variant->generateSku()]);
  }

  /**
   * Создание множественных вариантов (для вариативных товаров)
   */
  private function createMultipleVariants(Product $product, array $data): void
  {
    $variationConfig = [
      'variation_attributes' => $data['variation_attributes'],
      'selected_combinations' => $data['selected_combinations'],
      'prices' => $data['prices'],
    ];

    // Получаем все комбинации атрибутов
    $combinations = $this->generateAttributeCombinations($variationConfig['variation_attributes']);
    $createdVariants = [];

    foreach ($combinations as $combination) {
      // Пропускаем если админ не выбрал эту комбинацию
      if (!in_array($combination['key'], $variationConfig['selected_combinations'])) {
        continue;
      }

      // Создаем вариант
      $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'temp_' . time() . '_' . rand(1000, 9999), // Временный SKU
        'price' => $variationConfig['prices'][$combination['key']] ?? $data['price'],
        'is_default' => false, // Пока все не дефолтные
      ]);

      // Привязываем атрибуты вариации
      foreach ($combination['attributes'] as $attrId => $valueId) {
        ProductVariantAttribute::create([
          'variant_id' => $variant->id,
          'attribute_id' => $attrId,
          'attribute_value_id' => $valueId,
        ]);
      }

      // Обновляем SKU с учетом атрибутов
      $variant->update(['sku' => $variant->generateSku()]);
      $createdVariants[] = $variant;
    }

    // Устанавливаем самый дешевый как дефолтный
    if (!empty($createdVariants)) {
      $cheapest = collect($createdVariants)->sortBy('price')->first();
      $cheapest->update(['is_default' => true]);
    }
  }

  /**
   * Генерация SKU для простого варианта
   */
  private function generateSingleVariantSku(Product $product): string
  {
    return $product->base_sku ?? "product_{$product->id}";
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
  private function generateCombinationsRecursive(array $attributes, array $attributeIds, array $current, array &$combinations, int $index = 0): void
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

  public function update(Product $product, array $data): Product
  {
    return DB::transaction(function () use ($product, $data) {
      // 1. Обновляем основные данные товара
      $baseData = collect($data)->except(['variants'])->toArray();

      $product->update([
        'category_id' => $baseData['category_id'],
        'brand_id' => $baseData['brand_id'] ?? null,
        'base_sku' => $baseData['base_sku'] ?? null,
        'currency' => $baseData['currency'],
        'measurement' => $baseData['measurement'],
      ]);

      // 2. Обновляем описания
      foreach (['ru', 'ro', 'en'] as $lang) {
        $product->descriptions()->updateOrCreate(
          ['language' => $lang],
          [
            'title' => trim($baseData['descriptions'][$lang]['title']),
            'short_description' => trim($baseData['descriptions'][$lang]['short_description']),
            'full_description' => trim($baseData['descriptions'][$lang]['full_description'] ?? ''),
          ]
        );
      }

      // 3. Обновляем варианты товара
      $this->updateProductVariants($product, $data['variants'] ?? []);

      return $product->fresh();
    });
  }

  /**
   * Обновление вариантов товара
   */
  private function updateProductVariants(Product $product, array $variantsData): void
  {
    // Получаем существующие варианты
    $existingVariants = $product->variants()->with('variantAttributes')->get()->keyBy('id');
    $processedVariantIds = [];

    foreach ($variantsData as $variantData) {
      $variantId = $variantData['id'] ?? null;

      if ($variantId && $existingVariants->has($variantId)) {
        // Обновляем существующий вариант
        $variant = $existingVariants->get($variantId);
        $this->updateExistingVariant($variant, $variantData);
        $processedVariantIds[] = $variantId;
      } else {
        // Создаем новый вариант
        $newVariant = $this->createNewVariant($product, $variantData);
        $processedVariantIds[] = $newVariant->id;
      }
    }

    // Удаляем варианты которых нет в новых данных
    $variantsToDelete = $existingVariants->keys()->diff($processedVariantIds);
    if ($variantsToDelete->isNotEmpty()) {
      ProductVariant::whereIn('id', $variantsToDelete)->delete();
    }

    // Проверяем что остался хотя бы один дефолтный вариант
    $this->ensureDefaultVariant($product);
  }

  /**
   * Обновление существующего варианта
   */
  private function updateExistingVariant(ProductVariant $variant, array $variantData): void
  {
    // Обновляем основные данные варианта
    $variant->update([
      'price' => $variantData['price'],
      'is_default' => $variantData['is_default'] ?? false,
    ]);

    // Обновляем атрибуты варианта
    $this->updateVariantAttributes($variant, $variantData['attributes'] ?? []);
  }

  /**
   * Создание нового варианта
   */
  private function createNewVariant(Product $product, array $variantData): ProductVariant
  {
    // Создаем вариант с временным SKU
    $variant = ProductVariant::create([
      'product_id' => $product->id,
      'sku' => 'temp_' . time() . '_' . rand(1000, 9999),
      'price' => $variantData['price'],
      'is_default' => $variantData['is_default'] ?? false,
    ]);

    // Добавляем атрибуты к варианту
    $this->updateVariantAttributes($variant, $variantData['attributes'] ?? []);

    // Обновляем SKU с учетом атрибутов
    $variant->update(['sku' => $variant->generateSku()]);

    return $variant;
  }

  /**
   * Обновление атрибутов варианта
   */
  private function updateVariantAttributes(ProductVariant $variant, array $attributesData): void
  {
    // Удаляем все старые атрибуты варианта
    $variant->variantAttributes()->delete();

    // Добавляем новые атрибуты
    foreach ($attributesData as $attrData) {
      ProductVariantAttribute::create([
        'variant_id' => $variant->id,
        'attribute_id' => $attrData['attribute_id'],
        'attribute_value_id' => $attrData['value_id'],
      ]);
    }
  }

  /**
   * Проверка что есть дефолтный вариант, если нет - делаем первый дефолтным
   */
  private function ensureDefaultVariant(Product $product): void
  {
    $hasDefault = $product->variants()->where('is_default', true)->exists();

    if (!$hasDefault) {
      $firstVariant = $product->variants()->orderBy('id')->first();
      if ($firstVariant) {
        $firstVariant->update(['is_default' => true]);
      }
    }
  }

  public function destroy($product)
  {
    return DB::transaction(function () use ($product) {
      // Варианты удалятся каскадно благодаря внешним ключам
      return $product->delete();
    });
  }
}
