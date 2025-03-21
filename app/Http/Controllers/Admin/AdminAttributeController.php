<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class AdminAttributeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Attributes/IndexAttributes', [
            'attributes' => ProductAttribute::all()
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_ro' => 'required|string|max:255',
            'name_en' => 'required|string|max:255'
        ]);

        ProductAttribute::create([
            'name_ru' => mb_convert_case(trim($validated['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($validated['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($validated['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут успешно добавлен!');
    }
    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_ro' => 'required|string|max:255',
            'name_en' => 'required|string|max:255'
        ]);

        $attribute->update([
            'name_ru' => mb_convert_case(trim($validated['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($validated['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($validated['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут обновлён!');
    }
    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут удалён!');
    }
}
