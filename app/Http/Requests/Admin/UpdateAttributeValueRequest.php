<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'attribute_id' => 'required|exists:product_attributes,id',
            'value_ru' => 'required|string|max:255',
            'value_ro' => 'required|string|max:255',
            'value_en' => 'required|string|max:255',
        ];
    }
}
