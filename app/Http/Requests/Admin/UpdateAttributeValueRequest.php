<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_id' => 'required|exists:product_attributes,id',
            'translations.ru' => 'required|string|max:255',
            'translations.ro' => 'required|string|max:255',
            'translations.en' => 'required|string|max:255',
        ];
    }
}
