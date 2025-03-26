<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminBrandController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Brands/IndexBrand', [
            'brands' => Brand::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Brand::create([
            'name' => trim($validated['name'])
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Бренд добавлен!');
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $brand->update([
            'name' => trim($validated['name'])
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Бренд обновлён!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Бренд удалён!');
    }
}

