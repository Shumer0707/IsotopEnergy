<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;

class AdminProductController extends Controller
{
    public function create()
    {
        return Inertia::render('Products/Create');
    }
}
