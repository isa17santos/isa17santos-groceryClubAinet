<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('access-inventory');

        $filter = $request->get('filter');

        $products = match($filter) {
            'low' => Product::whereColumn('stock', '<', 'stock_lower_limit')
                ->where('stock', '>', 0)
                ->paginate(20),
            'out' => Product::where('stock', '<=', 0)->paginate(20),
            default => Product::paginate(20)->withQueryString(),
        };  

        return view('inventory.index', compact('products', 'filter'));
    }

    public function adjustForm(Product $product)
    {
        $this->authorize('access-inventory');

        return view('inventory.adjust', compact('product'));
    }

    public function adjust(Request $request, Product $product)
    {
        $this->authorize('access-inventory');

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


    public function adjustments()
    {
        $this->authorize('access-inventory');

        $adjustments = StockAdjustment::with(['product', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inventory.adjustments', compact('adjustments'));
    }

}
