<?php

namespace App\Http\Controllers;

use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\OrderRequest;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
  public function submit(OrderRequest $request)
  {
    try {
      $data = $request->validated();

      // Загружаем варианты с полной информацией
      $variants = ProductVariant::with([
        'product.description',
        'product.promotion.discountGroup',
        'variantAttributes.attribute.translations',
        'variantAttributes.attributeValue.translations'
      ])
        ->whereIn('id', array_keys($data['cart']))
        ->get();

      if ($variants->isEmpty()) {
        return response()->json(['error' => 'Варианты товаров не найдены'], 400);
      }

      // Формируем товары для письма (совместимость с шаблоном)
      $products = $variants->map(function ($variant) use ($data) {
        $quantity = $data['cart'][$variant->id];
        $basePrice = (float) $variant->price;

        // Вычисляем цену со скидкой
        $discountedPrice = $basePrice;
        if ($variant->product->promotion?->discountGroup?->discount_percent) {
          $discount = $variant->product->promotion->discountGroup->discount_percent;
          $discountedPrice = $basePrice * (1 - $discount / 100);
        }

        // Формируем атрибуты для отображения
        $attributes = $variant->variantAttributes->map(function ($va) {
          return $va->attribute->translatedName() . ': ' . $va->attributeValue->translatedValue();
        })->implode(', ');

        $productTitle = $variant->product->description->title ?? 'Без названия';
        if ($attributes) {
          $productTitle .= ' (' . $attributes . ')';
        }

        return [
          'title' => $productTitle,
          'sku' => $variant->sku,
          'price' => number_format($basePrice, 2, '.', ''),
          'discounted_price' => number_format($discountedPrice, 2, '.', ''),
          'qty' => $quantity,
          'total' => number_format($discountedPrice * $quantity, 2, '.', ''),
        ];
      });

      // Вычисляем итоги
      $totalOriginal = $products->sum(function ($product) {
        return (float) $product['price'] * $product['qty'];
      });

      $totalWithDiscount = $products->sum(function ($product) {
        return (float) $product['total'];
      });

      // Формируем полные данные для письма
      $emailData = array_merge($data, [
        'products' => $products->toArray(), // Для совместимости с шаблоном
        'total_original' => number_format($totalOriginal, 2, '.', ''),
        'total_with_discount' => number_format($totalWithDiscount, 2, '.', ''),
        'discount_amount' => number_format($totalOriginal - $totalWithDiscount, 2, '.', ''),
      ]);

      Log::info('Sending order email', $emailData);

      // Отправляем письма
      Mail::to('isotopenergy@gmail.com')->send(new OrderCreated($emailData));

      if (!empty($data['email'])) {
        Mail::to($data['email'])->send(new OrderCreated($emailData, true));
      }

      return response()->json(['message' => 'Заказ успешно создан']);
    } catch (\Exception $e) {
      Log::error('Order submission error: ' . $e->getMessage());
      Log::error('Stack trace: ' . $e->getTraceAsString());

      return response()->json([
        'error' => 'Ошибка создания заказа: ' . $e->getMessage()
      ], 500);
    }
  }
}
