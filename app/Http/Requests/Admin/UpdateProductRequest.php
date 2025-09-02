<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'category_id' => 'bail|required|integer|exists:categories,id',
      'brand_id' => 'nullable|integer|exists:brands,id',
      'price' => 'bail|required|numeric|min:0.01',
      'discount_price' => 'nullable|numeric|min:0',
      'currency' => 'bail|required|string|size:3',
      'descriptions' => 'required|array',
      'descriptions.ru.title' => 'bail|required|string|max:255',
      'descriptions.ru.short_description' => 'bail|required|string',
      'descriptions.ro.title' => 'bail|required|string|max:255',
      'descriptions.ro.short_description' => 'bail|required|string',
      'descriptions.en.title' => 'bail|required|string|max:255',
      'descriptions.en.short_description' => 'bail|required|string',
      'attributes' => 'nullable|array',
      'attributes.*.attribute_id' => 'bail|required|integer|exists:product_attributes,id',
      'attributes.*.value_id' => 'bail|required|integer|exists:attribute_values,id',
      'measurement' => 'nullable|string',
    ];
  }

  public function messages(): array
  {
    return [
      'category_id.required' => 'Выберите категорию.',
      'price.required' => 'Укажите цену.',
      'price.min' => 'Цена должна быть больше нуля.',
      'currency.size' => 'Валюта — 3 буквы, например MDL.',
      'descriptions.*.title.required' => 'Заполните название.',
      'descriptions.*.short_description.required' => 'Заполните краткое описание.',
      'attributes.*.attribute_id.required' => 'Выберите атрибут.',
      'attributes.*.value_id.required' => 'Выберите значение атрибута.',
    ];
  }
}
