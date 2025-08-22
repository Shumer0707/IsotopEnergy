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

    $prev = url()->previous(); // Referer
    $path = parse_url($prev, PHP_URL_PATH) ?: '/';
    $query = parse_url($prev, PHP_URL_QUERY);

    $segments = array_values(array_filter(explode('/', trim($path, '/'))));
    if (!empty($segments) && in_array($segments[0], $supported, true)) {
      array_shift($segments);
    }

    $newPath = '/' . $locale . (count($segments) ? '/' . implode('/', $segments) : '');
    $newUrl  = $newPath . ($query ? '?' . $query : '');

    return redirect($newUrl)->withCookie(cookie('lang', $locale, 60 * 24 * 30));
  }
}
