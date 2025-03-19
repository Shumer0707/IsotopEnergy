<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 🔹 Общедоступные маршруты
Route::get('/', function () {
    return Inertia::render('Home'); // Главная страница
})->name('home');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/admin/dashboard');
    }

    return back()->withErrors([
        'email' => 'Неверный email или пароль.',
    ]);
})->middleware('guest')->name('login.post');

Route::get('/about', function () {
    return Inertia::render('About'); // Страница "О нас"
})->name('about');

// 🔹 Маршруты для аутентификации (из Breeze)
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->middleware('guest')->name('login');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->middleware('auth')->name('logout');

// 🔹 Админская панель (только для администраторов)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
