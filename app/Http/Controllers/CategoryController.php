<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Product\ProductFilterService;

class CategoryController extends Controller
{
  public function show(Request $request, $id, ProductFilterService $filterService)
  {
    $category = Category::with([
      'children',
      'translation',
      'parent.translation',
      'parent.children.translation'
    ])->findOrFail($id);

    $brands = Brand::select('id', 'name')->get();

    $filters = json_decode($request->input('filters'), true) ?? [];
    $sort = $request->input('sort');

    $productsQuery = $filterService->filter($category, $filters, $sort);

    $max_price = ceil(($category->products()->max('price') ?? 10000) / 1000) * 1000;

    return Inertia::render('Products/ProductsByCategory', [
      'category' => $category,
      'parentCategory' => $category->parent,
      'products' => $productsQuery->paginate(4)->withQueryString(),
      'brands' => $brands,
      'max_price' => $max_price,
      'filters' => [
        'brands' => $filters['brands'] ?? [],
        'price_from' => $filters['price_from'] ?? 0,
        'price_to' => $filters['price_to'] ?? $max_price,
      ],
      'sort' => $sort,
    ]);
  }
}
