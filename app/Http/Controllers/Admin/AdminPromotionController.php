<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\DiscountGroup;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminPromotionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Promotions/IndexPromotions', [
            'promotions' => Promotion::with(['product', 'discountGroup', 'product.description'])->get(),
            'products' => Product::with('description')->get(),
            'discountGroups' => DiscountGroup::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'discount_group_id' => 'required|exists:discount_groups,id',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        Promotion::create([
            'product_id' => $request->product_id,
            'discount_group_id' => $request->discount_group_id,
            'starts_at' => now(),
            'ends_at' => $request->ends_at,
            'active' => true,
        ]);

        return redirect()->back()->with('message', 'Товар добавлен в акцию');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()->back()->with('message', 'Акция удалена');
    }
}
