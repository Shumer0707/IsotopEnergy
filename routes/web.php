<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
  PageController,
  ProductController,
  CategoryController,
  LayoutController,
  CartController,
  FavoriteController,
  OrderController,
  LanguageController,
  ContactController,
  TestController
};
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\ProductSearchController;

//
// 🔹 Статические страницы
//
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/favorites', [PageController::class, 'favorites'])->name('favorites');

//
// 🔹 Продукты и категории
//
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/promo-products', [LayoutController::class, 'promoProducts']);
Route::get('/search-products', ProductSearchController::class);

//
// 🔹 Общие данные
//
Route::get('/layout-data', [LayoutController::class, 'index'])->name('layout.data');
Route::get('/set-locale/{locale}', [LanguageController::class, 'switch'])->name('set-locale');

//
// 🔹 Корзина
//
Route::prefix('cart')->as('cart.')->group(function () {
  Route::post('/data', [CartController::class, 'index'])->name('data');
  Route::post('/add', [CartController::class, 'add'])->name('add');
  Route::post('/update', [CartController::class, 'update'])->name('update');
  Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
  Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
  Route::get('/get', [CartController::class, 'get'])->name('get');
});

//
// 🔹 Избранное
//
Route::prefix('favorites')->as('favorites.')->group(function () {
  Route::get('/data', [FavoriteController::class, 'index'])->name('data');
  Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
  Route::delete('/remove/{productId}', [FavoriteController::class, 'remove'])->name('remove');
  Route::delete('/clear', [FavoriteController::class, 'clear'])->name('clear');
});

//
// 🔹 Заказы и контакты
//
Route::post('/order', [OrderController::class, 'submit']);
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');

//
// 🔹 Аутентификация
//
Route::middleware('guest')->group(function () {
  Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
  ->middleware('auth')->name('logout');

//
// 🔹 Служебные маршруты
//
Route::get('/test', [TestController::class, 'index'])->name('test');

//
// 🔹 Админка (вынесена в отдельный файл)
//
Route::middleware(['auth', 'role:admin'])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () {
    require __DIR__ . '/admin.php';
  });

//
// 🔸 Fallback 404
//
Route::fallback(function () {
  return Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404);
});
