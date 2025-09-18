<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\AttributeValue;

use Illuminate\Support\Facades\DB;

class ProductFilterService
{
  public function filter(Category $category, array $filters = [], ?string $sort = null)
  {
    // ✅ ГЛАВНОЕ ИЗМЕНЕНИЕ: Теперь начинаем с товаров, но фильтруем по вариантам
    $query = $category->products()->with([
      'brand',
      'images',
      'description',
      'promotion.discountGroup',

      // ✅ НОВЫЕ СВЯЗИ: Загружаем варианты и их атрибуты
      'variants' => function ($q) {
        $q->orderBy('price', 'asc'); // Сначала дешевые варианты
      },
      'variants.variantAttributes.attribute.translations',
      'variants.variantAttributes.attributeValue.translations',

      // ✅ Загружаем дефолтный вариант отдельно (для отображения цены)
      'defaultVariant',
      'cheapestVariant',
    ]);

    // ✅ Бренды (без изменений)
    if (!empty($filters['brands'])) {
      $query->whereIn('brand_id', $filters['brands']);
    }

    // ✅ НОВАЯ ЛОГИКА: Фильтрация по атрибутам вариантов
    if (!empty($filters['attrs']) && is_array($filters['attrs'])) {
      foreach ($filters['attrs'] as $attrId => $valueIds) {
        if (empty($valueIds)) continue;

        // Фильтруем товары, у которых ЕСТЬ варианты с нужными атрибутами
        $query->whereHas('variants.variantAttributes', function ($q) use ($attrId, $valueIds) {
          $q->where('attribute_id', (int)$attrId)
            ->whereIn('attribute_value_id', array_map('intval', (array)$valueIds));
        });
      }
    }

    // ✅ НОВАЯ ЛОГИКА: Фильтрация по цене - ищем среди вариантов
    if (isset($filters['price_from']) || isset($filters['price_to'])) {
      $query->whereHas('variants', function ($q) use ($filters) {
        if (isset($filters['price_from'])) {
          $q->where('price', '>=', (int)$filters['price_from']);
        }
        if (isset($filters['price_to'])) {
          $q->where('price', '<=', (int)$filters['price_to']);
        }
      });
    }

    // ✅ НОВАЯ ЛОГИКА: Сортировка по цене - через варианты
    if ($sort === 'cheap') {
      // Сортируем по цене самого дешевого варианта
      $query->join('product_variants as pv_sort', function ($join) {
        $join->on('products.id', '=', 'pv_sort.product_id')
          ->where('pv_sort.is_default', true);
      })->orderBy('pv_sort.price', 'asc')
        ->select('products.*'); // Важно! Выбираем только поля products

    } elseif ($sort === 'expensive') {
      // Сортируем по цене самого дорогого варианта
      $query->leftJoin(
        DB::raw('(SELECT product_id, MAX(price) as max_price FROM product_variants GROUP BY product_id) as pv_max'),
        'products.id',
        '=',
        'pv_max.product_id'
      )->orderBy('pv_max.max_price', 'desc')
        ->select('products.*');
    }

    return $query;
  }

  public function availableFilters(Category $category, ?string $locale = null, bool $includeDescendants = false): array
  {
    $locale = $locale ?: app()->getLocale();

    // Если нужно учитывать подкатегории
    $categoryIds = [$category->id];

    // ✅ НОВАЯ ЛОГИКА: Ищем атрибуты среди вариантов товаров, а не самих товаров
    $base = DB::table('product_variant_attributes as pva')
      ->join('product_variants as pv', 'pv.id', '=', 'pva.variant_id')
      ->join('products', 'products.id', '=', 'pv.product_id')
      ->whereIn('products.category_id', $categoryIds)
      ->select('pva.attribute_id', 'pva.attribute_value_id', DB::raw('COUNT(DISTINCT products.id) as cnt'))
      ->groupBy('pva.attribute_id', 'pva.attribute_value_id')
      ->get();

    if ($base->isEmpty()) return [];

    $attrIds  = $base->pluck('attribute_id')->unique();
    $valueIds = $base->pluck('attribute_value_id')->unique();

    // Загружаем переводы атрибутов
    $attributes = ProductAttribute::with([
      'translations' => fn($q) => $q->where('language', $locale),
    ])->whereIn('id', $attrIds)->get()->keyBy('id');

    // Загружаем переводы значений
    $values = AttributeValue::with([
      'translations' => fn($q) => $q->where('language', $locale),
    ])->whereIn('id', $valueIds)->get()->keyBy('id');

    $byAttr = [];
    foreach ($base as $row) {
      $a = $attributes->get($row->attribute_id);
      $v = $values->get($row->attribute_value_id);
      if (!$a || !$v) continue;

      if (!isset($byAttr[$a->id])) {
        $byAttr[$a->id] = [
          'id'     => $a->id,
          // ✅ ИСПРАВЛЕНИЕ: Используем метод translatedName() из модели
          'name'   => $a->translatedName() ?? '—',
          'type'   => 'enum',
          'values' => [],
        ];
      }

      $byAttr[$a->id]['values'][] = [
        'id'    => $v->id,
        // ✅ ИСПРАВЛЕНИЕ: Используем метод translatedValue() из модели
        'label' => $v->translatedValue() ?? '—',
        'count' => (int) $row->cnt,
      ];
    }

    // Сортируем значения по алфавиту
    foreach ($byAttr as &$attr) {
      usort($attr['values'], fn($x, $y) => strnatcasecmp($x['label'], $y['label']));
    }

    return array_values($byAttr);
  }
}
