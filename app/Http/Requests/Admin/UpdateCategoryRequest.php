<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations.ru' => 'required|string|max:255',
            'translations.ro' => 'required|string|max:255',
            'translations.en' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ];
    }
}
