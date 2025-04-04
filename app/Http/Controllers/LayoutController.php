<?php

namespace App\Http\Controllers;

use App\Models\Category;

class LayoutController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->get(['id', 'name_ru', 'name_ro', 'name_en', 'parent_id']);

        return response()->json([
            'navCategories' => $categories
        ]);
    }
}
