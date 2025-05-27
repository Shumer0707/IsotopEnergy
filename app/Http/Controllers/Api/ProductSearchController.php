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
    $term = strtolower($request->input('q'));

    $products = Product::with('description')
      ->whereHas('description', function ($query) use ($term) {
        $query->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"]);
      })
      ->limit(10)
      ->get();

    return response()->json($products);
  }
}
