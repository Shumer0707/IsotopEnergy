<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Product\ProductFilterService;

class CategoryController extends Controller
{
    public function show(Request $request, $id, ProductFilterService $filterService)
    {
        $category = Category::with('children', 'translation')->findOrFail($id);
        $brands = Brand::select('id', 'name')->get();

        $filters = json_decode($request->input('filters'), true) ?? [];

        $products = $filterService->filter($category, $filters);

        return Inertia::render('Products/ProductsByCategory', [
            'category' => $category,
            'products' => $products,
            'brands' => $brands,
        ]);
    }
}
