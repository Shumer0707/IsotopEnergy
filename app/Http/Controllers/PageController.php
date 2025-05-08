<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    public function home()
    {
        return Inertia::render('Home');
    }
    public function contacts()
    {
        return Inertia::render('Contacts');
    }
    public function cart()
    {
        return Inertia::render('Cart');
    }
    public function about()
    {
        return Inertia::render('About');
    }
    public function favorites()
    {
        return Inertia::render('Favorites/IndexFavorites');
    }
}
