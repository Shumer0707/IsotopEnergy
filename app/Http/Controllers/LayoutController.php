<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;

class LayoutController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'translation',
            'children.translation',
        ])->whereNull('parent_id')->get();

        $brands = Brand::select('id', 'name', 'logo')->get();

        return response()->json([
            'navCategories' => $categories,
            'brands' => $brands,
        ]);
    }
}
