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

    if (!empty($filters['price_from']) && !empty($filters['price_to'])) {
      $query->whereBetween('price', [
        $filters['price_from'],
        $filters['price_to'],
      ]);
    }
    return $query;
  }
}
