<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\ProductVariant;

class CartController extends Controller
{
  /**
   * Получение данных о вариантах в корзине
   */
  public function index(Request $request)
  {
    $variantIds = $request->input('variant_ids', []);

    if (empty($variantIds)) {
      return response()->json(['variants' => []]);
    }

    // Загружаем варианты со всеми нужными связями
    $variants = ProductVariant::with([
      // Основной товар
      'product.description',
      'product.brand',
      'product.images',
      'product.promotion.discountGroup',

      // Атрибуты варианта с переводами
      'variantAttributes.attribute.translations',
      'variantAttributes.attributeValue.translations'
    ])
      ->whereIn('id', $variantIds)
      ->get()
      ->map(function ($variant) {
        // Добавляем вычисляемые поля для удобства фронта
        return [
          'id' => $variant->id,
          'sku' => $variant->sku,
          'price' => $variant->price,

          // Данные товара
          'product' => [
            'id' => $variant->product->id,
            'title' => $variant->product->description->title ?? 'Без названия',
            'brand_name' => $variant->product->brand->name ?? null,
            'main_image' => $variant->product->main_image,
            'currency' => $variant->product->currency ?? 'MDL',
          ],

          // Атрибуты варианта (размер, цвет и т.д.)
          'attributes' => $variant->variantAttributes->map(function ($va) {
            return [
              'name' => $va->attribute->translatedName(),
              'value' => $va->attributeValue->translatedValue(),
            ];
          }),

          // Цена со скидкой (если есть промо)
          'discounted_price' => $this->calculateDiscountedPrice($variant),
        ];
      });

    return response()->json(['variants' => $variants]);
  }

  /**
   * Добавление варианта в корзину
   */
  public function add(AddToCartRequest $request)
  {
    $data = $request->validated();

    // Проверяем что вариант существует
    $variant = ProductVariant::find($data['variant_id']);
    if (!$variant) {
      return response()->json(['error' => 'Вариант товара не найден'], 404);
    }

    $cart = session()->get('cart', []);
    $variantId = $data['variant_id'];
    $quantity = $data['quantity'] ?? 1;

    // Добавляем к существующему количеству
    $cart[$variantId] = ($cart[$variantId] ?? 0) + $quantity;

    session()->put('cart', $cart);

    return response()->json([
      'message' => 'Товар добавлен в корзину',
      'variant_id' => $variantId,
      'quantity' => $cart[$variantId],
      'total_items' => array_sum($cart)
    ]);
  }

  /**
   * Обновление количества варианта в корзине
   */
  public function update(UpdateCartRequest $request)
  {
    $data = $request->validated();

    $cart = session()->get('cart', []);
    $variantId = $data['variant_id'];
    $quantity = $data['quantity'];

    if ($quantity > 0) {
      $cart[$variantId] = $quantity;
    } else {
      // Если количество 0 - удаляем из корзины
      unset($cart[$variantId]);
    }

    session()->put('cart', $cart);

    return response()->json([
      'message' => $quantity > 0 ? 'Количество обновлено' : 'Товар удален',
      'variant_id' => $variantId,
      'quantity' => $quantity > 0 ? $quantity : 0,
      'total_items' => array_sum($cart)
    ]);
  }

  /**
   * Удаление варианта из корзины
   */
  public function remove($variantId)
  {
    $cart = session()->get('cart', []);

    if (isset($cart[$variantId])) {
      unset($cart[$variantId]);
      session()->put('cart', $cart);
    }

    return response()->json([
      'message' => 'Товар удален из корзины',
      'variant_id' => $variantId,
      'total_items' => array_sum($cart)
    ]);
  }

  /**
   * Полная очистка корзины
   */
  public function clear()
  {
    session()->forget('cart');

    return response()->json(['message' => 'Корзина очищена']);
  }

  /**
   * Получение содержимого корзины из сессии
   */
  public function get()
  {
    return response()->json([
      'items' => session('cart', []),
      'total_items' => array_sum(session('cart', []))
    ]);
  }

  /**
   * Вычисление цены со скидкой для варианта
   */
  private function calculateDiscountedPrice(ProductVariant $variant)
  {
    $basePrice = (float) $variant->price;

    // Проверяем есть ли скидка у товара
    if (
      $variant->product->promotion &&
      $variant->product->promotion->discountGroup &&
      $variant->product->promotion->discountGroup->discount_percent
    ) {

      $discount = $variant->product->promotion->discountGroup->discount_percent;
      return $basePrice * (1 - $discount / 100);
    }

    return $basePrice;
  }
}
