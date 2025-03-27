<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\BrandService;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use Inertia\Inertia;

class AdminBrandController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Brands/IndexBrand', [
            'brands' => Brand::all()
        ]);
    }

    public function store(StoreBrandRequest $request, BrandService $service)
    {
        $service->store($request->validated());

        return redirect()->route('admin.brands.index')->with('success', 'Бренд добавлен!');
    }

    public function update(UpdateBrandRequest $request, Brand $brand, BrandService $service)
    {
        $service->update($brand, $request->validated());

        return redirect()->route('admin.brands.index')->with('success', 'Бренд обновлён!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Бренд удалён!');
    }
}


