<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ShippingCostSetting;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

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

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function view(Request $request)
    {
        $cart = session('cart', []);
        $productIds = array_keys($cart);

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItemsArray = [];
        $total = 0;

        foreach ($cart as $productId => $data) {
            if (!isset($products[$productId])) continue;

            $product = $products[$productId];
            $quantity = $data['quantity'];

            $price = $product->has_discount && $quantity >= $product->discount_min_qty
                ? $product->discounted_price
                : $product->price;

            $subtotal = $quantity * $price;
            $total += $subtotal;

            $cartItemsArray[] = compact('product', 'quantity', 'price', 'subtotal');
        }

        $shipping = ShippingCostSetting::getCostForOrderTotal($total);
        $finalTotal = $total + $shipping;

        // Paginação 
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = array_slice($cartItemsArray, $offset, $perPage);

        $cartItems = new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($cartItemsArray),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        if (!in_array($user->type, ['member', 'board'])) {
            return back()->with('error', 'Only club members can make purchases.');
        }


        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();

        $total = 0;
        $items = [];
        $totalItems = 0;
        $hasOutOfStock = false;

        foreach ($products as $product) {
            $quantity = $cart[$product->id]['quantity'];
            $totalItems += $quantity;
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

        $card = $user->card;

        if (!$card || $card->balance < $finalTotal) {
            return back()->with('error', 'Insufficient funds in virtual card.');
        }

        $request->validate([
            'nif' => 'nullable|string|max:9',
            'delivery_address' => 'required|string|max:255',
        ]);

        $deliveryAddress = $request->input('delivery_address') ?? $user->default_delivery_address;

        $order = Order::create([
            'member_id' => $user->id,
            'status' => 'pending',
            'total_items' => $totalItems,
            'total' => $finalTotal,
            'shipping_cost' => $shipping,
            'nif' => $request->input('nif'),
            'delivery_address' => $deliveryAddress,
            'date' => Carbon::now(),
        ]);

        foreach ($items as $item) {
            $order->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['product']->discount ?? 0,
                'subtotal' => $item['subtotal'],
            ]);

            // Atualizar stock
            $product = Product::find($item['product_id']);
            $product->stock -= $item['quantity'];
            $product->save();
        }


        $card->balance -= $finalTotal;
        $card->save();


        session()->forget('cart');

        $message = 'Order is being prepared.';
        if ($hasOutOfStock) {
            $message .= ' Some products are out of stock and may delay delivery.';
        }

        return redirect()->route('catalog')->with('success', $message);
    }

}
