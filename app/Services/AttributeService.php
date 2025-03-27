<?php

namespace App\Services;

use App\Models\ProductAttribute;

class AttributeService
{
    public function store(array $data): ProductAttribute
    {
        return ProductAttribute::create([
            'name_ru' => mb_convert_case(trim($data['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($data['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($data['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);
    }

    public function update(ProductAttribute $attribute, array $data): ProductAttribute
    {
        $attribute->update([
            'name_ru' => mb_convert_case(trim($data['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($data['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($data['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return $attribute;
    }
}
