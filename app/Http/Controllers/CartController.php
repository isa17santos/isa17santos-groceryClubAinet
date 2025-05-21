<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ShippingCostSetting;


class CartController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = ['quantity' => $quantity];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    public function view()
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();

        $cartItems = [];
        $total = 0;
        
        foreach ($products as $product) {
            $quantity = $cart[$product->id]['quantity'];
            $price = $product->has_discount && $quantity >= $product->discount_min_qty
                ? $product->discounted_price
                : $product->price;
            $subtotal = $quantity * $price;
            $total += $subtotal;
            $cartItems[] = compact('product', 'quantity', 'price', 'subtotal');
        }

        $shipping = ShippingCostSetting::getCostForOrderTotal($total);

        $finalTotal = $total + $shipping;

        return view('cart.index', compact('cartItems', 'total', 'shipping', 'finalTotal'));
    }

    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity');

        if ($quantity < 1) {
            return $this->remove($request);
        }

        $cart = session('cart', []);
        $cart[$productId]['quantity'] = $quantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }

    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must log in to confirm purchase.');
        }

        $user = Auth::user();

        if ($user->role !== 'club_member') {
            return back()->with('error', 'Only club members can make purchases.');
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();

        $total = 0;
        $items = [];
        $hasOutOfStock = false;

        foreach ($products as $product) {
            $quantity = $cart[$product->id]['quantity'];
            $price = $product->has_discount && $quantity >= $product->discount_min_qty
                ? $product->discounted_price
                : $product->price;

            $subtotal = $quantity * $price;
            $total += $subtotal;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $price,
                'subtotal' => $subtotal
            ];

            if ($product->stock < $quantity) {
                $hasOutOfStock = true;
            }
        }

        $shipping = ShippingCostSetting::getCostForOrderTotal($total);

        $finalTotal = $total + $shipping;

        if ($user->virtual_card_balance < $finalTotal) {
            return back()->with('error', 'Insufficient funds in virtual card.');
        }

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'preparing',
            'total' => $finalTotal,
            'shipping_cost' => $shipping,
            'nif' => $request->input('nif'),
            'delivery_address' => $request->input('delivery_address'),
        ]);

        foreach ($items as $item) {
            $order->items()->create($item);
        }

        $user->virtual_card_balance -= $finalTotal;
        $user->save();

        session()->forget('cart');

        $message = 'Order is being prepared.';
        if ($hasOutOfStock) {
            $message .= ' Some products are out of stock and may delay delivery.';
        }

        return redirect()->route('catalog')->with('success', $message);
    }

}
