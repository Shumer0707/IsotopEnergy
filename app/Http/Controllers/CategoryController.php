<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function show($id)
{
    $category = Category::with('children')->findOrFail($id);

    // Подкатегория → получаем товары
    $products = $category->products()->with('brand', 'images')->get();

    return Inertia::render('Products/ProductsByCategory', [
        'category' => $category,
        'products' => $products,
    ]);
}
}
