<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDiscountGroupController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/DiscountGroups/IndexDiscountGroups', [
            'discountGroups' => DiscountGroup::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_percent' => 'required|integer|min:1|max:100',
        ]);

        DiscountGroup::create($request->only('name', 'discount_percent'));

        return redirect()->back()->with('message', 'Группа скидки добавлена');
    }

    public function destroy(DiscountGroup $discountGroup)
    {
        $discountGroup->delete();

        return redirect()->back()->with('message', 'Группа скидки удалена');
    }
}
