<?php

namespace App\Services;

use App\Models\ProductAttribute;

class AttributeService
{
    public function getIndexData(): array
    {
        return [
            'attributes' => ProductAttribute::with('translations')->get()
        ];
    }

    public function store(array $data): ProductAttribute
    {
        $attribute = ProductAttribute::create();

        foreach ($data['translations'] as $lang => $name) {
            $attribute->translations()->create([
                'language' => $lang,
                'name' => mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8'),
            ]);
        }

        return $attribute;
    }

    public function update(ProductAttribute $attribute, array $data): ProductAttribute
    {
        foreach ($data['translations'] as $lang => $name) {
            $attribute->translations()->updateOrCreate(
                ['language' => $lang],
                ['name' => mb_convert_case(trim($name), MB_CASE_TITLE, 'UTF-8')]
            );
        }

        return $attribute;
    }
}
