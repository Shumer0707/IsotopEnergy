<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $rules = [
      'category_id' => 'bail|required|integer|exists:categories,id',
      'brand_id' => 'nullable|integer|exists:brands,id',
      'price' => 'bail|required|numeric|min:0.01',
      'discount_price' => 'nullable|numeric|min:0',
      'currency' => 'bail|required|string|size:3',
      'descriptions' => 'required|array',
      'descriptions.ru.title' => 'bail|required|string|max:255',
      'descriptions.ru.short_description' => 'bail|required|string',
      'descriptions.ru.full_description' => 'nullable|string',
      'descriptions.ro.title' => 'bail|required|string|max:255',
      'descriptions.ro.short_description' => 'bail|required|string',
      'descriptions.ro.full_description' => 'nullable|string',
      'descriptions.en.title' => 'bail|required|string|max:255',
      'descriptions.en.short_description' => 'bail|required|string',
      'descriptions.en.full_description' => 'nullable|string',
      'attributes' => 'nullable|array',
      'attributes.*.attribute_id' => 'bail|required|integer|exists:product_attributes,id',
      'attributes.*.value_id' => 'bail|required|integer|exists:attribute_values,id',
    ];

    // Если создаем вариации товара
    if ($this->has('create_variations') && $this->create_variations) {
      $rules = array_merge($rules, [
        'create_variations' => 'boolean',
        'variation_attributes' => 'required_if:create_variations,true|array|min:1',
        'variation_attributes.*' => 'array|min:1',
        'selected_combinations' => 'required_if:create_variations,true|array|min:1',
        'prices' => 'required_if:create_variations,true|array',
        'prices.*' => 'numeric|min:0.01',
        'use_common_images' => 'nullable|boolean',
        'common_images' => 'nullable|array',
        'common_images.*' => 'file|image|max:10240',
        'variation_images' => 'nullable|array',
        'variation_images.*' => 'array',
        'variation_images.*.*' => 'file|image|max:10240', // 10MB макс для изображения
      ]);
    }

    return $rules;
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

      // Сообщения для вариаций
      'variation_attributes.required_if' => 'Для создания вариаций выберите атрибуты.',
      'variation_attributes.min' => 'Выберите хотя бы один атрибут для вариаций.',
      'variation_attributes.*.min' => 'Для каждого атрибута выберите хотя бы одно значение.',
      'selected_combinations.required_if' => 'Выберите хотя бы одну комбинацию для создания.',
      'selected_combinations.min' => 'Выберите хотя бы одну комбинацию.',
      'prices.required_if' => 'Укажите цены для всех выбранных вариаций.',
      'prices.*.numeric' => 'Все цены должны быть числами.',
      'prices.*.min' => 'Цены должны быть больше нуля.',
    ];
  }

  /**
   * Проверяем, создается ли товар с вариациями
   */
  public function isCreatingVariations(): bool
  {
    return $this->has('create_variations') && $this->create_variations;
  }

  /**
   * Получаем базовые данные товара (без данных вариаций)
   */
  public function getBaseProductData(): array
  {
    return collect($this->validated())
      ->except(['create_variations', 'variation_attributes', 'selected_combinations', 'prices'])
      ->toArray();
  }

  /**
   * Получаем данные для создания вариаций
   */
  public function getVariationConfig(): array
  {
    if (!$this->isCreatingVariations()) {
      return [];
    }

    return collect($this->validated())
      ->only(['variation_attributes', 'selected_combinations', 'prices'])
      ->toArray();
  }
}
