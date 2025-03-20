<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show($id)
    {
        // Временно передаем фиктивные данные
        $product = [
            'id' => $id,
            'name' => 'Пример товара',
            'description' => 'Описание товара. Это тестовые данные.',
            'price' => '1000',
            'currency' => 'MDL',
        ];

        return Inertia::render('Product', [
            'product' => $product,
        ]);
    }
}
