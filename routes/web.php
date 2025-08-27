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
  ContactController,
  TestController,
  LanguageController,
  SitemapController,
};
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\ProductSearchController;

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])
  ->name('sitemap')
  ->withoutMiddleware(\App\Http\Middleware\HandleInertiaRequests::class);

// 🔹 Редирект с корня на дефолтный язык
Route::get('/', fn() => redirect('/ru'));

// 🔹 ПУБЛИЧНЫЕ СТРАНИЦЫ (SEO) под {locale}
Route::group([
  'prefix' => '{locale}',
  'where'  => ['locale' => 'ru|ro'],
], function () {
  // Страницы
  Route::get('/', [PageController::class, 'home'])->name('home');
  Route::get('/about', [PageController::class, 'about'])->name('about');
  Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
  Route::get('/cart', [PageController::class, 'cart'])->name('cart');
  Route::get('/favorites', [PageController::class, 'favorites'])->name('favorites');

  // Карточки/категории (страницы)
  Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
  Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.show');

  // 404 внутри локали (страницы)
  Route::fallback(
    fn() =>
    Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404)
  );
});

Route::get('/set-locale/{locale}', [LanguageController::class, 'switch'])->name('set-locale');

// 🔹 СЛУЖЕБНЫЕ / XHR / API (БЕЗ локали в URL)
Route::get('/layout-data', [LayoutController::class, 'index'])->name('layout.data');
Route::get('/promo-products', [LayoutController::class, 'promoProducts']);
Route::get('/search-products', ProductSearchController::class);

// Корзина (XHR)
Route::prefix('cart')->as('cart.')->group(function () {
  Route::post('/data', [CartController::class, 'index'])->name('data');
  Route::post('/add', [CartController::class, 'add'])->name('add');
  Route::post('/update', [CartController::class, 'update'])->name('update');
  Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
  Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
  Route::get('/get', [CartController::class, 'get'])->name('get');
});

// Избранное (XHR)
Route::prefix('favorites')->as('favorites.')->group(function () {
  Route::get('/data', [FavoriteController::class, 'index'])->name('data');
  Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
  Route::delete('/remove/{productId}', [FavoriteController::class, 'remove'])->name('remove');
  Route::delete('/clear', [FavoriteController::class, 'clear'])->name('clear');
});

// Заказы/контакты (XHR)
Route::post('/order', [OrderController::class, 'submit']);
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');

// Аутентификация (страницы без локали — ок, это не SEO-контент)
Route::middleware('guest')->group(function () {
  Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
  ->middleware('auth')->name('logout');

// Служебное
Route::get('/test', [TestController::class, 'index'])->name('test');


// 🔹 Админка (вынесена в отдельный файл)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
  require __DIR__ . '/admin.php';
});

// Глобальный 404 вне локали
Route::fallback(
  fn() =>
  Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404)
);
