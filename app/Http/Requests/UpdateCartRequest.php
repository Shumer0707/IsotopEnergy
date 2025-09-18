<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      // Также переходим на variant_id
      'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
      'quantity' => ['required', 'integer', 'min:1', 'max:100'],
    ];
  }

  public function messages(): array
  {
    return [
      'variant_id.required' => 'ID варианта товара обязателен',
      'variant_id.exists' => 'Выбранный вариант товара не найден',
      'quantity.required' => 'Количество обязательно',
      'quantity.min' => 'Количество должно быть больше 0',
      'quantity.max' => 'Максимальное количество: 100',
    ];
  }
}
