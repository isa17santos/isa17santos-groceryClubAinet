<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter');

        $products = match($filter) {
            'low' => Product::whereColumn('stock', '<', 'stock_lower_limit')->get(),
            'out' => Product::where('stock', 0)->get(),
            default => Product::paginate(20)->withQueryString(),
        };

        return view('inventory.index', compact('products', 'filter'));
    }

    public function adjustForm(Product $product)
    {
        return view('inventory.adjust', compact('product'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer']);

        $diff = $request->input('quantity') - $product->stock;

        DB::transaction(function () use ($product, $diff, $request) {
            $product->update(['stock' => $request->input('quantity')]);

            StockAdjustment::create([
                'product_id' => $product->id,
                'registered_by_user_id' => auth()->id(),
                'quantity_changed' => $diff,
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Stock adjusted.');
    }


}
