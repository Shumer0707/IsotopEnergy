<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\App;

class ProductSearchController extends Controller
{
  public function __invoke(Request $request)
  {
    $term = $request->input('q');

    $descriptionIds = \App\Models\Description::search($term)
      ->get()
      ->pluck('product_id')
      ->unique()
      ->take(10);

    $products = \App\Models\Product::with('description')
      ->whereIn('id', $descriptionIds)
      ->get();

    return response()->json($products);
  }
}
