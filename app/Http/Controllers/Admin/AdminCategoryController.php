<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categories/IndexCategory', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_ro' => 'required|string|max:255',
            'name_en' => 'required|string|max:255'
        ]);

        Category::create([
            'name_ru' => mb_convert_case(trim($validated['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($validated['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($validated['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Категория успешно добавлена!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_ro' => 'required|string|max:255',
            'name_en' => 'required|string|max:255'
        ]);

        $category->update([
            'name_ru' => mb_convert_case(trim($validated['name_ru']), MB_CASE_TITLE, 'UTF-8'),
            'name_ro' => mb_convert_case(trim($validated['name_ro']), MB_CASE_TITLE, 'UTF-8'),
            'name_en' => mb_convert_case(trim($validated['name_en']), MB_CASE_TITLE, 'UTF-8'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Категория обновлена!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Категория удалена!');
    }
}
