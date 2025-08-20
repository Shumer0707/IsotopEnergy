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
    $query = $category->products()->with([
      'brand',
      'images',
      'description',
      'promotion.discountGroup',
      'attributeValues.value.translation',
    ]);

    // Бренды
    if (!empty($filters['brands'])) {
      $query->whereIn('brand_id', $filters['brands']);
    }

    // Атрибуты: AND по атрибутам, OR по значениям
    if (!empty($filters['attrs']) && is_array($filters['attrs'])) {
      foreach ($filters['attrs'] as $attrId => $valueIds) {
        if (empty($valueIds)) continue;
        $query->whereHas('attributeValues', function ($q) use ($attrId, $valueIds) {
          $q->where('attribute_id', (int)$attrId)
            ->whereIn('attribute_value_id', array_map('intval', (array)$valueIds));
        });
      }
    }

    // Цена
    if (isset($filters['price_from'])) $query->where('price', '>=', (int)$filters['price_from']);
    if (isset($filters['price_to']))   $query->where('price', '<=', (int)$filters['price_to']);

    // Сортировка
    if ($sort === 'cheap')      $query->orderBy('price', 'asc');
    elseif ($sort === 'expensive') $query->orderBy('price', 'desc');

    return $query;
  }

  public function availableFilters(Category $category, ?string $locale = null, bool $includeDescendants = false): array
  {
    $locale = $locale ?: app()->getLocale();

    // Если нужно учитывать подкатегории, собери их id и подставь в whereIn
    $categoryIds = [$category->id];

    $base = DB::table('product_attribute_values as pav')
      ->join('products', 'products.id', '=', 'pav.product_id')
      ->whereIn('products.category_id', $categoryIds)
      ->select('pav.attribute_id', 'pav.attribute_value_id', DB::raw('COUNT(*) as cnt'))
      ->groupBy('pav.attribute_id', 'pav.attribute_value_id')
      ->get();

    if ($base->isEmpty()) return [];

    $attrIds  = $base->pluck('attribute_id')->unique();
    $valueIds = $base->pluck('attribute_value_id')->unique();

    $attributes = ProductAttribute::with([
      'translation' => fn($q) => $q->where('language', $locale),
    ])->whereIn('id', $attrIds)->get()->keyBy('id');

    $values = AttributeValue::with([
      'translation' => fn($q) => $q->where('language', $locale),
    ])->whereIn('id', $valueIds)->get()->keyBy('id');

    $byAttr = [];
    foreach ($base as $row) {
      $a = $attributes->get($row->attribute_id);
      $v = $values->get($row->attribute_value_id);
      if (!$a || !$v) continue;

      if (!isset($byAttr[$a->id])) {
        $byAttr[$a->id] = [
          'id'     => $a->id,
          'name'   => $a->translation->name ?? '—',
          'type'   => 'enum',
          'values' => [],
        ];
      }

      $byAttr[$a->id]['values'][] = [
        'id'    => $v->id,
        'label' => $v->translation->value ?? '—',
        'count' => (int) $row->cnt,
      ];
    }

    foreach ($byAttr as &$attr) {
      usort($attr['values'], fn($x, $y) => strnatcasecmp($x['label'], $y['label']));
    }

    return array_values($byAttr);
  }
}
