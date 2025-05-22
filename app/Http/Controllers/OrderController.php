<?php

namespace App\Http\Controllers;

use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\OrderRequest;
use App\Models\Product;

class OrderController extends Controller
{
  public function submit(OrderRequest $request)
  {
    $data = $request->validated();

    // Получаем товары из БД
    $products = Product::with('description')
      ->whereIn('id', array_keys($data['cart']))
      ->get()
      ->map(function ($product) use ($data) {
        $qty = $data['cart'][$product->id];
        $price = floatval($product->discounted_price ?? $product->price);
        $total = $price * $qty;

        return [
          'id' => $product->id,
          'title' => $product->description->title ?? 'Без названия',
          'price' => $product->price,
          'discounted_price' => $price,
          'qty' => $qty,
          'total' => number_format($total, 2, '.', ''),
        ];
      })
      ->values()
      ->all();

    $data['products'] = $products;

    $totalOriginal = 0;
    $totalWithDiscount = 0;

    foreach ($data['products'] as $product) {
      $totalOriginal += $product['price'] * $product['qty'];
      $totalWithDiscount += $product['discounted_price'] * $product['qty'];
    }

    $data['total_original'] = number_format($totalOriginal, 2, '.', '');
    $data['total_with_discount'] = number_format($totalWithDiscount, 2, '.', '');
    $data['discount_amount'] = number_format($totalOriginal - $totalWithDiscount, 2, '.', '');

    // Письмо менеджеру
    Mail::to('shumer0707@gmail.com')->send(new OrderCreated($data));

    // Письмо клиенту
    if (!empty($data['email'])) {
      Mail::to($data['email'])->send(new OrderCreated($data, true));
    }
    return response()->json(['message' => 'Заказ отправлен']);
  }
}
