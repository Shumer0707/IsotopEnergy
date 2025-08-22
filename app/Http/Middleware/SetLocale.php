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
    $supported = ['ru', 'ro'];

    $locale = $request->route('locale')
      ?? Session::get('locale')
      ?? $request->cookie('lang')
      ?? 'ru';

    if (!in_array($locale, $supported, true)) {
      $locale = 'ru';
    }

    App::setLocale($locale);
    Session::put('locale', $locale);

    $response = $next($request);
    return $response->withCookie(cookie('lang', $locale, 60 * 24 * 30));
  }
}
