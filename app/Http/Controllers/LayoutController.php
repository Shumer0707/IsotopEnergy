<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class LayoutController extends Controller
{
  public function index()
  {
    $categories = Category::with([
      'translation',
      'children.translation',
    ])->whereNull('parent_id')->get();

    $brands = Brand::select('id', 'name', 'logo')->get();

    $promoProducts = Product::with(['description', 'promotion.discountGroup'])
      ->whereHas('promotion', fn($q) => $q->where('active', true))
      ->get();

    return response()->json([
      'navCategories' => $categories,
      'brands' => $brands,
      'promoProducts' => $promoProducts,
    ]);
  }

  public function promoProducts()
  {
    $products = Product::with(['description', 'promotion.discountGroup'])
      ->whereHas('promotion', fn($q) => $q->where('active', true))
      ->paginate(8);

    return response()->json($products);
  }
}
