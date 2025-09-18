<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      // Теперь работаем с ID варианта вместо товара
      'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
      'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
    ];
  }

  public function messages(): array
  {
    return [
      'variant_id.required' => 'ID варианта товара обязателен',
      'variant_id.exists' => 'Выбранный вариант товара не найден',
      'quantity.min' => 'Количество должно быть больше 0',
      'quantity.max' => 'Максимальное количество: 100',
    ];
  }
}
