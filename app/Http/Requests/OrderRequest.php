<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'first_name' => ['required', 'string', 'max:255'],
      'last_name' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:20'],
      'email' => ['nullable', 'email', 'max:255'],
      'comment' => ['nullable', 'string', 'max:1000'],
      'delivery_method' => ['required', 'in:pickup,delivery'],
      'payment_method' => ['required', 'in:cash,card,invoice'],

      // Адрес (опционально)
      'address' => ['nullable', 'array'],
      'address.country' => ['required_if:delivery_method,delivery', 'string', 'max:100'],
      'address.region' => ['required_if:delivery_method,delivery', 'string', 'max:100'],
      'address.city' => ['required_if:delivery_method,delivery', 'string', 'max:100'],
      'address.zip' => ['nullable', 'string', 'max:20'],
      'address.street' => ['required_if:delivery_method,delivery', 'string', 'max:255'],

      // Корзина с variant_id
      'cart' => ['required', 'array', 'min:1'],
    ];
  }

  // Кастомная валидация для корзины
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->has('cart')) {
        foreach ($this->cart as $variantId => $quantity) {
          if (!is_numeric($variantId) || !is_numeric($quantity) || $quantity < 1 || $quantity > 100) {
            $validator->errors()->add('cart', 'Некорректные данные корзины');
            break;
          }
        }
      }
    });
  }
}
