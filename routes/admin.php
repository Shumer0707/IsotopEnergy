<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    DashboardController,
    AdminProductController,
    AdminCategoryController,
    AdminAttributeController,
    AdminAttributeValueController,
    AdminBrandController,
    AdminImageController,
    AdminPromotionController,
    AdminDiscountGroupController
};

//
// 🔹 Панель администратора
//
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//
// 🔹 Продукты
//
Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
Route::post('/products/update/{product}', [AdminProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

//
// 🔹 Изображения товаров
//
Route::post('/products/{product}/images', [AdminImageController::class, 'uploadImages'])->name('products.images.upload');
Route::delete('/products/images/{image}', [AdminImageController::class, 'deleteImage'])->name('products.images.delete');
Route::get('/products/{product}/images/list', [AdminImageController::class, 'imagesList']);
Route::put('/products/{product}/main-image', [AdminImageController::class, 'setMainImage']);

//
// 🔹 Категории
//
Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
Route::post('/categories/store', [AdminCategoryController::class, 'store'])->name('categories.store');
Route::post('/categories/update/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

//
// 🔹 Атрибуты
//
Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
Route::post('/attributes/store', [AdminAttributeController::class, 'store'])->name('attributes.store');
Route::post('/attributes/update/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
Route::delete('/attributes/{attribute}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');

//
// 🔹 Значения атрибутов
//
Route::get('/attribute-values', [AdminAttributeValueController::class, 'index'])->name('attribute-values.index');
Route::post('/attribute-values/store', [AdminAttributeValueController::class, 'store'])->name('attribute-values.store');
Route::post('/attribute-values/update/{attributeValue}', [AdminAttributeValueController::class, 'update'])->name('attribute-values.update');
Route::delete('/attribute-values/{attributeValue}', [AdminAttributeValueController::class, 'destroy'])->name('attribute-values.destroy');

//
// 🔹 Бренды
//
Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
Route::post('/brands/store', [AdminBrandController::class, 'store']);
Route::post('/brands/update/{brand}', [AdminBrandController::class, 'update']);
Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy']);

//
// 🔹 Акции
//
Route::get('/promotions', [AdminPromotionController::class, 'index'])->name('promotions.index');
Route::post('/promotions', [AdminPromotionController::class, 'store'])->name('promotions.store');
Route::delete('/promotions/{promotion}', [AdminPromotionController::class, 'destroy'])->name('promotions.destroy');

//
// 🔹 Группы скидок
//
Route::get('/discount-groups', [AdminDiscountGroupController::class, 'index'])->name('discount-groups.index');
Route::post('/discount-groups', [AdminDiscountGroupController::class, 'store'])->name('discount-groups.store');
Route::delete('/discount-groups/{discountGroup}', [AdminDiscountGroupController::class, 'destroy'])->name('discount-groups.destroy');
