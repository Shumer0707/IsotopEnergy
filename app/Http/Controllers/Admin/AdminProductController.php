<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

class AdminProductController extends Controller
{
    public function index(ProductService $service)
    {
        $categoryId = request('category');

        return Inertia::render('Admin/Products/IndexProducts', $service->getIndexData($categoryId));
    }
    public function store(StoreProductRequest $request, ProductService $service)
    {
        $service->store($request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Товар добавлен!');
    }

    public function update(UpdateProductRequest $request, Product $product, ProductService $service)
    {
        $service->update($product, $request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Товар обновлён!');
    }

    public function destroy(Product $product, ProductService $service)
    {
        $service->destroy($product);
        return redirect()->route('admin.products.index')->with('success', 'Товар удалён!');
    }
}
