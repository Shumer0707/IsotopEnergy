<?php

namespace App\Services\Product;

use App\Models\Category;

class ProductFilterService
{
    public function filter(Category $category, array $filters = [])
    {
        $query = $category->products()->with('brand', 'images', 'description');

        if (!empty($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }

        return $query->get();
    }
}
