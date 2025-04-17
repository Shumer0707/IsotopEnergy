<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['description', 'brand', 'category', 'images', 'attributeValues.value.translation']);

        return Inertia::render('Product', [
            'product' => $product,
        ]);
    }
}
