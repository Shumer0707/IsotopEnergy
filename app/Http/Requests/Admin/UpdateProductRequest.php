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
      // Основные поля товара
      'category_id' => 'bail|required|integer|exists:categories,id',
      'brand_id' => 'nullable|integer|exists:brands,id',
      'base_sku' => 'nullable|string|max:255',
      'currency' => 'bail|required|string|size:3',
      'measurement' => 'nullable|string',

      // Описания на трех языках
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

      // Варианты товара (обязательно должен быть хотя бы один)
      'variants' => 'required|array|min:1',
      'variants.*.id' => 'nullable|integer|exists:product_variants,id',
      'variants.*.price' => 'bail|required|numeric|min:0.01',
      'variants.*.is_default' => 'boolean',
      'variants.*.attributes' => 'nullable|array',
      'variants.*.attributes.*.attribute_id' => 'bail|required|integer|exists:product_attributes,id',
      'variants.*.attributes.*.value_id' => 'bail|required|integer|exists:attribute_values,id',
    ];
  }

  public function messages(): array
  {
    return [
      // Основные поля
      'category_id.required' => 'Выберите категорию.',
      'brand_id.exists' => 'Выбранный бренд не существует.',
      'base_sku.max' => 'Артикул не должен превышать 255 символов.',
      'currency.size' => 'Валюта должна состоять из 3 букв, например MDL.',
      'measurement.string' => 'Единица измерения должна быть строкой.',

      // Описания
      'descriptions.required' => 'Описания обязательны.',
      'descriptions.ru.title.required' => 'Заполните название на русском.',
      'descriptions.ru.title.max' => 'Название на русском не должно превышать 255 символов.',
      'descriptions.ru.short_description.required' => 'Заполните краткое описание на русском.',
      'descriptions.ro.title.required' => 'Заполните название на румынском.',
      'descriptions.ro.title.max' => 'Название на румынском не должно превышать 255 символов.',
      'descriptions.ro.short_description.required' => 'Заполните краткое описание на румынском.',
      'descriptions.en.title.required' => 'Заполните название на английском.',
      'descriptions.en.title.max' => 'Название на английском не должно превышать 255 символов.',
      'descriptions.en.short_description.required' => 'Заполните краткое описание на английском.',

      // Варианты
      'variants.required' => 'У товара должен быть хотя бы один вариант.',
      'variants.min' => 'У товара должен быть хотя бы один вариант.',
      'variants.*.price.required' => 'Укажите цену для каждого варианта.',
      'variants.*.price.numeric' => 'Цена должна быть числом.',
      'variants.*.price.min' => 'Цена должна быть больше нуля.',
      'variants.*.is_default.boolean' => 'Поле "по умолчанию" должно быть true/false.',

      // Атрибуты вариантов
      'variants.*.attributes.*.attribute_id.required' => 'Выберите атрибут.',
      'variants.*.attributes.*.attribute_id.exists' => 'Выбранный атрибут не существует.',
      'variants.*.attributes.*.value_id.required' => 'Выберите значение атрибута.',
      'variants.*.attributes.*.value_id.exists' => 'Выбранное значение не существует.',
    ];
  }

  /**
   * Получить базовые данные товара (без вариантов)
   */
  public function getBaseProductData(): array
  {
    return collect($this->validated())
      ->except(['variants'])
      ->toArray();
  }

  /**
   * Получить данные вариантов
   */
  public function getVariantsData(): array
  {
    return $this->validated('variants', []);
  }

  /**
   * Проверка что есть хотя бы один дефолтный вариант
   */
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $variants = $this->input('variants', []);

      if (empty($variants)) {
        $validator->errors()->add('variants', 'У товара должен быть хотя бы один вариант.');
        return;
      }

      // Проверяем что есть хотя бы один дефолтный вариант
      $hasDefault = false;
      foreach ($variants as $index => $variant) {
        if (!empty($variant['is_default']) && $variant['is_default']) {
          $hasDefault = true;
          break;
        }
      }

      if (!$hasDefault) {
        $validator->errors()->add('variants', 'Один из вариантов должен быть помечен как "по умолчанию".');
      }

      // Проверяем что дефолтный вариант только один
      $defaultCount = 0;
      foreach ($variants as $variant) {
        if (!empty($variant['is_default']) && $variant['is_default']) {
          $defaultCount++;
        }
      }

      if ($defaultCount > 1) {
        $validator->errors()->add('variants', 'Только один вариант может быть помечен как "по умолчанию".');
      }

      // Валидация атрибутов вариантов
      foreach ($variants as $variantIndex => $variant) {
        if (!empty($variant['attributes'])) {
          $attributeIds = [];

          foreach ($variant['attributes'] as $attrIndex => $attribute) {
            $attributeId = $attribute['attribute_id'] ?? null;

            // Проверяем дублирование атрибутов в одном варианте
            if (in_array($attributeId, $attributeIds)) {
              $validator->errors()->add(
                "variants.{$variantIndex}.attributes.{$attrIndex}.attribute_id",
                'Атрибут уже добавлен к этому варианту.'
              );
            }

            $attributeIds[] = $attributeId;
          }
        }
      }
    });
  }
}
