<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
  public function switch(Request $request, string $locale)
  {
    $supported = ['ru', 'ro'];
    if (!in_array($locale, $supported, true)) {
      return redirect()->back();
    }

    Session::put('locale', $locale);

    // Получаем текущий путь из заголовка Referer
    $referer = $request->header('referer');
    $path = '/';

    if ($referer) {
      $parsed = parse_url($referer);
      $path = $parsed['path'] ?? '/';
      $query = $parsed['query'] ?? '';
    }

    // Убираем /public/ если он есть в пути
    $path = preg_replace('#^/public/#', '/', $path);

    // Разбиваем путь на сегменты
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));

    // Убираем старую локаль если она есть
    if (!empty($segments) && in_array($segments[0], $supported, true)) {
      array_shift($segments);
    }

    // Формируем новый путь
    $newPath = '/' . $locale . (count($segments) ? '/' . implode('/', $segments) : '');
    $newUrl = $newPath . (!empty($query) ? '?' . $query : '');

    return redirect($newUrl)->withCookie(cookie('lang', $locale, 60 * 24 * 30));
  }
}
