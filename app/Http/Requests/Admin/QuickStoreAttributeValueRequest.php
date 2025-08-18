<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuickStoreAttributeValueRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'attribute_id'       => 'bail|required|integer|exists:product_attributes,id',
      'translations'       => 'required|array',
      'translations.ru'    => 'bail|required|string|max:255',
      'translations.ro'    => 'nullable|string|max:255',
      'translations.en'    => 'nullable|string|max:255',
    ];
  }

  public function messages(): array
  {
    return [
      'attribute_id.required' => 'Сначала выберите атрибут.',
      'translations.ru.required' => 'Введите значение на русском (ru).',
    ];
  }
}
