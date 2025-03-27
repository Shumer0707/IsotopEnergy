<?php

namespace App\Services;

use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeValueService
{


    public function getIndexData(?int $attributeId = null): array
    {
        $query = AttributeValue::with('attribute');

        if ($attributeId) {
            $query->where('attribute_id', $attributeId);
        }

        return [
            'values' => $query->get(),
            'attributes' => \App\Models\ProductAttribute::all(),
            'activeAttribute' => $attributeId,
        ];
    }

    public function store(array $data): AttributeValue
    {
        return AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
            'value_ru' => mb_convert_case(trim($data['value_ru']), MB_CASE_TITLE, 'UTF-8'),
            'value_ro' => mb_convert_case(trim($data['value_ro']), MB_CASE_TITLE, 'UTF-8'),
            'value_en' => mb_convert_case(trim($data['value_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);
    }

    public function update(AttributeValue $value, array $data): AttributeValue
    {
        $value->update([
            'attribute_id' => $data['attribute_id'],
            'value_ru' => mb_convert_case(trim($data['value_ru']), MB_CASE_TITLE, 'UTF-8'),
            'value_ro' => mb_convert_case(trim($data['value_ro']), MB_CASE_TITLE, 'UTF-8'),
            'value_en' => mb_convert_case(trim($data['value_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return $value;
    }
}
