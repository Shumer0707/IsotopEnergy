<?php

namespace App\Http\Controllers;

use App\Models\Category;

class LayoutController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'translation',
            'children.translation',
        ])->whereNull('parent_id')->get();

        // Можем вернуть как есть, и на фронте использовать translatedName()
        return response()->json([
            'navCategories' => $categories
        ]);
    }
}
