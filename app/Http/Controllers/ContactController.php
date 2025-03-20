<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Отправляем письмо
        Mail::to('admin@example.com')->send(new ContactMail($validated));

        return back()->with('success', 'Ваше сообщение отправлено!');
    }
}
