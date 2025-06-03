<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShippingCostSetting;

class ShippingCostController extends Controller
{
    public function index()
    {
        $shippingCosts = ShippingCostSetting::orderBy('min_value_threshold')->get();
        return view('shipping_costs.index', compact('shippingCosts'));
    }

    public function create()
    {
        return view('shipping_costs.create');

    }

    public function store(Request $request)
    {
        $request->validate([
            'min_value_threshold' => 'required|numeric|min:0',
            'max_value_threshold' => 'required|numeric|gt:min_value_threshold',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        ShippingCostSetting::create($request->all());
        return redirect()->route('shipping-costs.index')->with('success', 'Shipping cost added.');
    }

    public function edit(ShippingCostSetting $shippingCost)
    {
        return view('shipping_costs.edit', compact('shippingCost'));
    }

    public function update(Request $request, ShippingCostSetting $shippingCost)
    {
        $request->validate([
            'min_value_threshold' => 'required|numeric|min:0',
            'max_value_threshold' => 'required|numeric|gt:min_value_threshold',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $shippingCost->update($request->all());
        return redirect()->route('shipping-costs.index')->with('success', 'Shipping cost updated.');
    }

    public function destroy(ShippingCostSetting $shippingCost)
    {
        $shippingCost->delete();
        return redirect()->route('shipping-costs.index')->with('success', 'Shipping cost deleted.');
    }
}
