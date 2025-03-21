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

// 🔹 Общедоступные маршруты
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');

// 🔹 Категории товаров
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

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
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/store', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::post('/categories/update/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/attributes', [AdminAttributeController::class, 'index'])->name('attributes.index');
    Route::post('/attributes/store', [AdminAttributeController::class, 'store'])->name('attributes.store');
    Route::post('/attributes/update/{attribute}', [AdminAttributeController::class, 'update'])->name('attributes.update');
    Route::delete('/attributes/{attribute}', [AdminAttributeController::class, 'destroy'])->name('attributes.destroy');
});

Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
