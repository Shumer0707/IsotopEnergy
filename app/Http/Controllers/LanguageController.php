<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        if (in_array($locale, ['ru', 'ro'])) {
            Session::put('locale', $locale);
            return redirect()->back(); // Возвращаем пользователя на ту же страницу
        }

        return redirect()->back();
    }
}
