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
      'phone' => 'required|string|max:50',
      'email' => 'nullable|email',
      'message' => 'required|string',
      'agree' => 'accepted',
    ]);

    // Отправляем письмо
    Mail::to('isotopenergy@gmail.com')->send(new ContactMail($validated));

    return back()->with('success', true);
  }
}
