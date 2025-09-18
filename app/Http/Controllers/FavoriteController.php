<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
  public function toggle(Request $request)
  {
    $productId = $request->input('product_id');
    $favorites = session()->get('favorites', []);

    if (in_array($productId, $favorites)) {
      $favorites = array_diff($favorites, [$productId]);
    } else {
      $favorites[] = $productId;
    }

    session()->put('favorites', $favorites);

    return response()->json(['message' => 'Избранное обновлено', 'favorites' => array_values($favorites)]);
  }

  public function index()
  {
    $favorites = session()->get('favorites', []);

    // ✅ ОБНОВЛЯЕМ: Загружаем все связи как в каталоге
    $products = Product::with([
      'description',
      'brand',
      'images',

      // Варианты с атрибутами и переводами
      'variants' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ])->orderBy('price', 'asc');
      },

      // Дефолтный вариант
      'defaultVariant' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ]);
      },

      // Самый дешевый вариант
      'cheapestVariant' => function ($query) {
        $query->with([
          'variantAttributes.attribute.translations',
          'variantAttributes.attributeValue.translations'
        ]);
      },

      'promotion.discountGroup',
    ])
      ->whereIn('id', $favorites)
      ->get();

    return response()->json([
      'products' => $products,
    ]);
  }

  public function remove($productId)
  {
    $favorites = session()->get('favorites', []);
    $favorites = array_diff($favorites, [$productId]);

    session()->put('favorites', array_values($favorites));

    return response()->json(['favorites' => array_values($favorites)]);
  }

  public function clear()
  {
    session()->forget('favorites');

    return response()->json(['favorites' => []]);
  }
}
