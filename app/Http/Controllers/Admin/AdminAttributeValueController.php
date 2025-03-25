<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class AdminAttributeValueController extends Controller
{
    public function index(Request $request)
    {
        $attributeId = $request->query('attribute');

        $query = AttributeValue::with('attribute');

        if ($attributeId) {
            $query->where('attribute_id', $attributeId);
        }

        return Inertia::render('Admin/AttributeValues/IndexAttributeValues', [
            'values' => $query->get(),
            'attributes' => ProductAttribute::all(),
            'activeAttribute' => $attributeId,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
            'value_ru' => 'required|string|max:255',
            'value_ro' => 'required|string|max:255',
            'value_en' => 'required|string|max:255',
        ]);

        AttributeValue::create([
            'attribute_id' => $validated['attribute_id'],
            'value_ru' => mb_convert_case(trim($validated['value_ru']), MB_CASE_TITLE, 'UTF-8'),
            'value_ro' => mb_convert_case(trim($validated['value_ro']), MB_CASE_TITLE, 'UTF-8'),
            'value_en' => mb_convert_case(trim($validated['value_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение добавлено!');
    }
    public function update(Request $request, AttributeValue $attributeValue)
    {
        $validated = $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
            'value_ru' => 'required|string|max:255',
            'value_ro' => 'required|string|max:255',
            'value_en' => 'required|string|max:255',
        ]);

        $attributeValue->update([
            'attribute_id' => $validated['attribute_id'],
            'value_ru' => mb_convert_case(trim($validated['value_ru']), MB_CASE_TITLE, 'UTF-8'),
            'value_ro' => mb_convert_case(trim($validated['value_ro']), MB_CASE_TITLE, 'UTF-8'),
            'value_en' => mb_convert_case(trim($validated['value_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение обновлено!');
    }
    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();

        return redirect()->route('admin.attribute-values.index')->with('success', 'Значение удалено!');
    }
}
