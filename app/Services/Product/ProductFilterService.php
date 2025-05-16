<?php

namespace App\Services\Product;

use App\Models\Category;

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

    if (!empty($filters['brands'])) {
      $query->whereIn('brand_id', $filters['brands']);
    }

    if ($sort === 'cheap') {
      $query->orderBy('price', 'asc');
    } elseif ($sort === 'expensive') {
      $query->orderBy('price', 'desc');
    }

    if (isset($filters['price_from'])) {
      $query->where('price', '>=', $filters['price_from']);
    }
    if (isset($filters['price_to'])) {
      $query->where('price', '<=', $filters['price_to']);
    }
    return $query;
  }
}
