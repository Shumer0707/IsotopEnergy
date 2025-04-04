<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class AttributeValueService
{
    public function getIndexData(?int $attributeId = null): array
    {
        $query = AttributeValue::with(['translations', 'attribute.translation']);

        if ($attributeId) {
            $query->where('attribute_id', $attributeId);
        }

        return [
            'values' => $query->get(),
            'attributes' => ProductAttribute::with('translation')->get(),
            'activeAttribute' => $attributeId,
        ];
    }

    public function store(array $data): AttributeValue
    {
        $value = AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
        ]);

        foreach ($data['translations'] as $lang => $val) {
            $value->translations()->create([
                'language' => $lang,
                'value' => mb_convert_case(trim($val), MB_CASE_TITLE, 'UTF-8'),
            ]);
        }

        return $value;
    }

    public function update(AttributeValue $value, array $data): AttributeValue
    {
        $value->update([
            'attribute_id' => $data['attribute_id'],
        ]);

        foreach ($data['translations'] as $lang => $val) {
            $value->translations()->updateOrCreate(
                ['language' => $lang],
                ['value' => mb_convert_case(trim($val), MB_CASE_TITLE, 'UTF-8')]
            );
        }

        return $value;
    }
}
