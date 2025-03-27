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
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'currency' => 'required|string|size:3',
            'descriptions' => 'required|array',
            'descriptions.ru.title' => 'required|string|max:255',
            'descriptions.ru.short_description' => 'required|string',
            'descriptions.ro.title' => 'required|string|max:255',
            'descriptions.ro.short_description' => 'required|string',
            'descriptions.en.title' => 'required|string|max:255',
            'descriptions.en.short_description' => 'required|string',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:product_attributes,id',
            'attributes.*.value_id' => 'required|exists:attribute_values,id',
        ];
    }
}
