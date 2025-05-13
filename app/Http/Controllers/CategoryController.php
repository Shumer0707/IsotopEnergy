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
    $category = Category::with('children', 'translation', 'parent.translation', 'parent.children.translation')->findOrFail($id);
    $brands = Brand::select('id', 'name')->get();

    $filters = json_decode($request->input('filters'), true) ?? [];
$sort = $request->input('sort');
    $products = $filterService->filter($category, $filters, $sort)->paginate(4)->withQueryString();

    return Inertia::render('Products/ProductsByCategory', [
      'category' => $category,
      'parentCategory' => $category->parent,
      'products' => $products,
      'brands' => $brands,
    ]);
  }
}
