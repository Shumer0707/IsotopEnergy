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
      'base_sku' => 'nullable|string|max:255|unique:products,base_sku',
      'currency' => 'bail|required|string|size:3',
      'measurement' => 'nullable|string',

      // Изображения товара
      'images' => 'nullable|array',
      'images.*' => 'file|image|max:10240', // 10MB макс для изображения

      // Описания
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

      // Обычные атрибуты (для простых товаров)
      'attributes' => 'nullable|array',
      'attributes.*.attribute_id' => 'bail|required|integer|exists:product_attributes,id',
      'attributes.*.value_id' => 'bail|required|integer|exists:attribute_values,id',
    ];

    // Если создаем вариации товара
    if ($this->has('create_variations') && $this->create_variations) {
      $rules = array_merge($rules, [
        'create_variations' => 'boolean',
        'price_step' => 'nullable|numeric|min:0',

        // Атрибуты для вариаций
        'variation_attributes' => 'required_if:create_variations,true|array|min:1',
        'variation_attributes.*' => 'array|min:1',
        'variation_attributes.*.*' => 'integer|exists:attribute_values,id',

        // Выбранные комбинации и их цены
        'selected_combinations' => 'required_if:create_variations,true|array|min:1',
        'selected_combinations.*' => 'string',
        'prices' => 'required_if:create_variations,true|array',
        'prices.*' => 'numeric|min:0.01',
      ]);

      // Убираем валидацию обычных атрибутов для вариативных товаров
      unset($rules['attributes']);
      unset($rules['attributes.*.attribute_id']);
      unset($rules['attributes.*.value_id']);
    }

    return $rules;
  }

  public function messages(): array
  {
    return [
      // Основные поля
      'category_id.required' => 'Выберите категорию.',
      'price.required' => 'Укажите базовую цену.',
      'price.min' => 'Цена должна быть больше нуля.',
      'base_sku.max' => 'Артикул не должен превышать 255 символов.',
      'currency.size' => 'Валюта — 3 буквы, например MDL.',
      'measurement.string' => 'Единица измерения должна быть строкой.',

      // Изображения
      'images.*.file' => 'Загружайте только файлы.',
      'images.*.image' => 'Загружайте только изображения.',
      'images.*.max' => 'Размер изображения не должен превышать 10MB.',

      // Описания
      'descriptions.*.title.required' => 'Заполните название.',
      'descriptions.*.title.max' => 'Название не должно превышать 255 символов.',
      'descriptions.*.short_description.required' => 'Заполните краткое описание.',

      // Обычные атрибуты
      'attributes.*.attribute_id.required' => 'Выберите атрибут.',
      'attributes.*.attribute_id.exists' => 'Выбранный атрибут не существует.',
      'attributes.*.value_id.required' => 'Выберите значение атрибута.',
      'attributes.*.value_id.exists' => 'Выбранное значение не существует.',

      // Вариации
      'create_variations.boolean' => 'Поле создания вариаций должно быть true/false.',
      'price_step.numeric' => 'Шаг цены должен быть числом.',
      'price_step.min' => 'Шаг цены не может быть отрицательным.',

      'variation_attributes.required_if' => 'Для создания вариаций выберите атрибуты.',
      'variation_attributes.min' => 'Выберите хотя бы один атрибут для вариаций.',
      'variation_attributes.*.min' => 'Для каждого атрибута выберите хотя бы одно значение.',
      'variation_attributes.*.*.exists' => 'Одно из выбранных значений не существует.',

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
    $data = collect($this->validated())
      ->except(['create_variations', 'variation_attributes', 'selected_combinations', 'prices', 'price_step', 'attributes'])
      ->toArray();

    // Добавляем обычные атрибуты только для простых товаров
    if (!$this->isCreatingVariations()) {
      $data['attributes'] = $this->validated('attributes', []);
    }

    return $data;
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
      ->only(['variation_attributes', 'selected_combinations', 'prices', 'price_step'])
      ->toArray();
  }

  /**
   * Получаем изображения товара
   */
  public function getImages(): array
  {
    return $this->file('images', []);
  }

  /**
   * Проверяем корректность комбинаций и цен
   */
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->isCreatingVariations()) {
        // Проверяем что для каждой выбранной комбинации указана цена
        $selectedCombinations = $this->input('selected_combinations', []);
        $prices = $this->input('prices', []);

        foreach ($selectedCombinations as $combination) {
          if (!isset($prices[$combination]) || empty($prices[$combination])) {
            $validator->errors()->add('prices', "Не указана цена для комбинации: {$combination}");
          }
        }

        // Проверяем что не указаны цены для невыбранных комбинаций
        foreach (array_keys($prices) as $combination) {
          if (!in_array($combination, $selectedCombinations)) {
            $validator->errors()->add('selected_combinations', "Указана цена для невыбранной комбинации: {$combination}");
          }
        }
      }
    });
  }
}
