<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class PageController extends Controller
{
  public function home(Request $request, $locale = 'ru')
  {
    // Устанавливаем локаль
    app()->setLocale($locale);

    // Получаем переводы для SEO
    $seoData = [
      'title' => __('messages.home_meta_title'),
      'description' => __('messages.home_meta_description'),
      'canonical' => $request->url(),
      'og_title' => __('messages.home_meta_title'),
      'og_description' => __('messages.home_meta_description'),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('Home', [
      'locale' => $locale,
      'seo' => $seoData,
    ]);
  }

  public function about(Request $request, $locale = 'ru')
  {
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.about_meta_title'),
      'description' => __('messages.about_meta_description'),
      'canonical' => $request->url(),
      'og_title' => __('messages.about_meta_title'),
      'og_description' => __('messages.about_meta_description'),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('About', [
      'locale' => $locale,
      'seo' => $seoData,
    ]);
  }

  public function contacts(Request $request, $locale = 'ru')
  {
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.contacts_meta_title'),
      'description' => __('messages.contacts_meta_description'),
      'canonical' => $request->url(),
      'og_title' => __('messages.contacts_meta_title'),
      'og_description' => __('messages.contacts_meta_description'),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('Contacts', [
      'locale' => $locale,
      'seo' => $seoData,
    ]);
  }

  public function cart(Request $request, $locale = 'ru')
  {
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.cart_title') . ' - IsotopEnergy',
      'description' => 'Корзина покупок - IsotopEnergy термопанели',
      'canonical' => $request->url(),
    ];

    view()->share('seoData', $seoData);
    view()->share('locale', $locale);

    return Inertia::render('Cart', [
      'locale' => $locale,
      'seo' => $seoData,
    ]);
  }

  public function favorites(Request $request, $locale = 'ru')
  {
    app()->setLocale($locale);

    $seoData = [
      'title' => __('messages.favorite_title') . ' - IsotopEnergy',
      'description' => 'Избранные товары - IsotopEnergy',
      'canonical' => $request->url(),
    ];

    return Inertia::render('Favorites/IndexFavorites', [
      'locale' => $locale,
      'seo' => $seoData,
    ]);
  }
}
