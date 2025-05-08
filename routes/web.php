<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminAttributeController;
use App\Http\Controllers\Admin\AdminAttributeValueController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AdminPromotionController;
use App\Http\Controllers\Admin\AdminDiscountGroupController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TestController;

// 🔹 Общедоступные маршруты
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/favorites', [PageController::class, 'favorites'])->name('favorites');

Route::get('/layout-data', [LayoutController::class, 'index'])->name('layout.data');
Route::get('/promo-products', [LayoutController::class, 'promoProducts']);

Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');

Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

Route::prefix('cart')->as('cart.')->group(function () {
  Route::post('/data', [CartController::class, 'data'])->name('data');
  Route::post('/add', [CartController::class, 'add'])->name('add');
  Route::post('/update', [CartController::class, 'update'])->name('update');
  Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
  Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::prefix('favorites')->as('favorites.')->group(function () {
  Route::get('/data', [FavoriteController::class, 'index'])->name('data');
  Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
  Route::delete('/remove/{productId}', [FavoriteController::class, 'remove'])->name('remove');
  Route::delete('/clear', [FavoriteController::class, 'clear'])->name('clear');
});

Route::post('/order', [OrderController::class, 'submit']);

Route::get('/set-locale/{locale}', [LanguageController::class, 'switch'])->name('set-locale');

// 🔹 Аутентификация (Breeze)
Route::middleware('guest')->group(function () {
  Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// 🔹 Админская панель (только для администраторов)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

  Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
  Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
  Route::post('/products/update/{product}', [AdminProductController::class, 'update'])->name('products.update');
  Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

  Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
  Route::post('/categories/store', [AdminCategoryController::class, 'store'])->name('categories.store');
  Route::post('/categories/update/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
  Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

  Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
  Route::post('/attributes/store', [AdminAttributeController::class, 'store'])->name('attributes.store');
  Route::post('/attributes/update/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
  Route::delete('/attributes/{attribute}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');

  Route::get('/attribute-values', [AdminAttributeValueController::class, 'index'])->name('attribute-values.index');
  Route::post('/attribute-values/store', [AdminAttributeValueController::class, 'store'])->name('attribute-values.store');
  Route::post('/attribute-values/update/{attributeValue}', [AdminAttributeValueController::class, 'update'])->name('attribute-values.update');
  Route::delete('/attribute-values/{attributeValue}', [AdminAttributeValueController::class, 'destroy'])->name('attribute-values.destroy');

  Route::post('/products/{product}/images', [AdminImageController::class, 'uploadImages'])->name('products.images.upload');
  Route::delete('/products/images/{image}', [AdminImageController::class, 'deleteImage'])->name('products.images.delete');
  Route::get('/products/{product}/images/list', [AdminImageController::class, 'imagesList']);
  Route::put('/products/{product}/main-image', [AdminImageController::class, 'setMainImage']);

  Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
  Route::post('/brands/store', [AdminBrandController::class, 'store']);
  Route::post('/brands/update/{brand}', [AdminBrandController::class, 'update']);
  Route::delete('/brands/{brand}', [AdminBrandController::class, 'destroy']);

  Route::get('/promotions', [AdminPromotionController::class, 'index'])->name('promotions.index');
  Route::post('/promotions', [AdminPromotionController::class, 'store'])->name('promotions.store');
  Route::delete('/promotions/{promotion}', [AdminPromotionController::class, 'destroy'])->name('promotions.destroy');

  Route::get('discount-groups', [AdminDiscountGroupController::class, 'index'])->name('discount-groups.index');
  Route::post('discount-groups', [AdminDiscountGroupController::class, 'store'])->name('discount-groups.store');
  Route::delete('discount-groups/{discountGroup}', [AdminDiscountGroupController::class, 'destroy'])->name('discount-groups.destroy');
});

Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');


// 🔹 Общедоступные маршруты
Route::get('/test', [TestController::class, 'index'])->name('test');
