<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // Разрешаем всем
  }

  public function rules(): array
  {
    return [
      'first_name' => 'required|string|max:255',
      'last_name'  => 'required|string|max:255',
      'phone'      => 'required|string|max:255',
      'email'      => 'nullable|email|max:255',
      'comment'    => 'nullable|string',
      'cart'       => 'required|array|min:1',
      'payment_method' => 'required|string|in:cash,card,invoice',
      'delivery_method' => 'required|string|in:pickup,delivery',
      'address'    => 'nullable|array',
      'address.country' => 'required_if:delivery_method,delivery|string|max:255',
      'address.region'  => 'required_if:delivery_method,delivery|string|max:255',
      'address.city'    => 'required_if:delivery_method,delivery|string|max:255',
      'address.street'  => 'required_if:delivery_method,delivery|string|max:255',
    ];
  }
}
