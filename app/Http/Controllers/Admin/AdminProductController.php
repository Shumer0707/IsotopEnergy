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
    $productData = $request->getBaseProductData();
    $productData['images'] = $request->getImages();
    $productData['create_variations'] = $request->isCreatingVariations();

    if ($request->isCreatingVariations()) {
      $productData = array_merge($productData, $request->getVariationConfig());
    }

    $product = $service->store($productData);

    if ($request->isCreatingVariations()) {
      $variantsCount = $product->variants()->count();
      $message = "Товар создан с {$variantsCount} вариантами!";
    } else {
      $message = 'Товар добавлен!';
    }
    return redirect()->route('admin.products.index')->with('success', $message);
  }

  public function update(UpdateProductRequest $request, Product $product, ProductService $service)
  {
    $baseData = $request->getBaseProductData();
    $variantsData = $request->getVariantsData();

    // Объединяем данные
    $productData = array_merge($baseData, ['variants' => $variantsData]);

    $service->update($product, $productData);

    return redirect()->route('admin.products.index')->with('success', 'Товар обновлён!');
  }

  public function destroy(Product $product, ProductService $service)
  {
    $service->destroy($product);
    return redirect()->route('admin.products.index')->with('success', 'Товар удалён!');
  }
}
