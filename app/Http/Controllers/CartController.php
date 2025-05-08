<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;

class CartController extends Controller
{
  public function data(Request $request)
  {
    $productIds = $request->input('product_ids', []);

    $products = Product::with(['description', 'brand', 'images', 'promotion'])
      ->whereIn('id', $productIds)
      ->get();

    return response()->json([
      'products' => $products,
    ]);
  }
  public function add(AddToCartRequest $request)
  {
    $data = $request->validated();

    $cart = session()->get('cart', []);

    $productId = $data['product_id'];
    $quantity = $data['quantity'] ?? 1;

    $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;

    session()->put('cart', $cart);

    return response()->json(['message' => 'Товар добавлен в корзину']);
  }

  public function update(UpdateCartRequest $request)
  {
    $data = $request->validated();

    $cart = session()->get('cart', []);
    $cart[$data['product_id']] = $data['quantity'];

    session()->put('cart', $cart);

    return response()->json(['message' => 'Количество обновлено']);
  }

  public function remove($productId)
  {
    $cart = session()->get('cart', []);

    if (isset($cart[$productId])) {
      unset($cart[$productId]);
      session()->put('cart', $cart);
    }

    return response()->json(['message' => 'Товар удалён']);
  }

  public function clear()
  {
    session()->forget('cart');

    return response()->json(['message' => 'Корзина очищена']);
  }
}
