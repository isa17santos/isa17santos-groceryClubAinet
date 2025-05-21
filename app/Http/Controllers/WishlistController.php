<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = session('wishlist', []);
        $products = Product::whereIn('id', $wishlist)->with('category')->get();

        return view('wishlist.index', compact('products'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = session('wishlist', []);

        if (!in_array($request->product_id, $wishlist)) {
            $wishlist[] = $request->product_id;
            session(['wishlist' => $wishlist]);
        }

        return back()->with('success', 'Product added to wishlist!');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = session('wishlist', []);
        session(['wishlist' => array_filter($wishlist, fn($id) => $id != $request->product_id)]);

        return back()->with('success', 'Product removed from wishlist.');
    }
}
