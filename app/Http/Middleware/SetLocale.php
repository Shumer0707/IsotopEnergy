<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Если передан новый язык, обновляем session
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }
        // Если язык уже есть в session — используем его
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Если нет, пробуем взять из cookie
        elseif ($request->cookie('lang')) {
            $locale = $request->cookie('lang');
            Session::put('locale', $locale);
        }
        // Если ничего нет, ставим ru
        else {
            $locale = 'ru';
            Session::put('locale', $locale);
        }

        App::setLocale($locale);

        // Устанавливаем куку с языком (независимо от способа определения)
        $response = $next($request);
        return $response->withCookie(cookie('lang', $locale, 60 * 24 * 30)); // 30 дней
    }
}
