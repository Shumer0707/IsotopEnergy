<?php

namespace App\Http\Controllers;

use App\Mail\OrderCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'comment' => 'nullable|string',
            'cart' => 'required|array|min:1',
        ]);

        // Отправка письма
        Mail::to('you@example.com')->send(new OrderCreated($data));

        return response()->json(['message' => 'Заказ отправлен']);
    }
}
