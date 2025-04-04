<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class TestController
{
    public function index()
    {
        // $product = Product::with('description')->first();

    $category = Category::with('translation')->get();
    foreach($category as $el){
        $res[] = ($el->translation->name);
    }

    }

}
