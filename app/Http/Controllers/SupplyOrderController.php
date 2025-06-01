<?php

namespace App\Http\Controllers;

use App\Models\SupplyOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplyOrderController extends Controller
{
    public function index()
    {
        $orders = SupplyOrder::with(['product', 'user'])
            ->orderByRaw("CASE WHEN status = 'requested' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('supply_orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $oldOrders = old('orders', $request->input('orders', []));
        $orderLines = max(1, count($oldOrders));

        if ($request->has('add_row')) {
            $orderLines++;
        }
        if ($request->has('remove_row')) {
            $orderLines = max(1, $orderLines - 1);
        }

        session()->flashInput(['orders' => $oldOrders]);

        return view('supply_orders.create', compact('products', 'orderLines'));

    }




    public function store(Request $request)
    {
        $orders = $request->input('orders', []);

        // Ações baseadas no botão clicado
        if ($request->has('add_row')) {
            $orders[] = ['product_id' => '', 'quantity' => ''];
            return back()->withInput(['orders' => $orders]);
        }

        if ($request->has('remove_row')) {
            $index = (int) $request->input('remove_row');
            unset($orders[$index]);
            $orders = array_values($orders); // Reindexar
            return back()->withInput(['orders' => $orders]);
        }

        // Validação normal
        $validated = $request->validate([
            'orders' => 'required|array|min:1',
            'orders.*.product_id' => 'required|exists:products,id',
            'orders.*.quantity' => 'required|integer|min:1',
        ], [
            'orders.required' => 'At least one supply order line is required.',
            'orders.*.product_id.required' => 'Please select a product.',
            'orders.*.product_id.exists' => 'The selected product is invalid.',
            'orders.*.quantity.required' => 'Please enter a quantity.',
            'orders.*.quantity.integer' => 'Quantity must be an integer.',
            'orders.*.quantity.min' => 'Quantity must be at least 1.',
        ]);

        foreach ($validated['orders'] as $orderData) {
            SupplyOrder::create([
                'product_id' => $orderData['product_id'],
                'quantity' => $orderData['quantity'],
                'registered_by_user_id' => auth()->id(),
                'status' => 'requested',
            ]);
        }

        return redirect()->route('supply_orders.index')->with('success', 'Supply orders created successfully.');
    }




    public function generateAutomatically()
    {
        $products = Product::whereColumn('stock', '<', 'stock_lower_limit')->get();

        foreach ($products as $product) {
            $quantity = $product->stock_upper_limit - $product->stock;
            if ($quantity > 0) {
                SupplyOrder::create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'status' => 'requested',
                    'registered_by_user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('supply_orders.index')->with('success', 'Automatic supply orders created.');
    }

    public function markAsCompleted(SupplyOrder $order)
    {
        if ($order->status !== 'requested') {
            return back()->withErrors('Only requested orders can be completed.');
        }

        $order->update(['status' => 'completed']);
        $order->product->increment('stock', $order->quantity);

        return redirect()->route('supply_orders.index')->with('success', 'Supply order marked as completed and stock updated.');
    }

    public function destroy(SupplyOrder $order)
    {
        if ($order->status !== 'requested') {
            return back()->withErrors('Only requested orders can be deleted.');
        }

        $order->delete();

        return redirect()->route('supply_orders.index')->with('success', 'Supply order deleted.');
    }
}
