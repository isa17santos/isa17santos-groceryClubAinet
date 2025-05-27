<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Auth::check()
            ? Auth::user()->getWishlist()
            : session('wishlist', []);

        $products = Product::whereIn('id', $wishlist)->with('category')->get();

        return view('wishlist.index', compact('products'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $productId = (int) $request->product_id;

        if (Auth::check()) {
            $user = Auth::user();
            $wishlist = $user->getWishlist();

            if (!in_array($productId, $wishlist)) {
                $wishlist[] = $productId;
                $user->setWishlist($wishlist);
                session(['wishlist' => $wishlist]);
            }
        } else {
            // Visitante (não autenticado)
            $wishlist = session('wishlist', []);
            if (!in_array($productId, $wishlist)) {
                $wishlist[] = $productId;
                session(['wishlist' => $wishlist]);
            }
        }

        return back()->with('success', 'Product added to wishlist!');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $productId = (int) $request->product_id;

        if (Auth::check()) {
            $user = Auth::user();
            $wishlist = array_filter($user->getWishlist(), fn($id) => $id != $productId);
            $user->setWishlist(array_values($wishlist)); // reindexar
            session(['wishlist' => $wishlist]);
        } else {
            $wishlist = array_filter(session('wishlist', []), fn($id) => $id != $productId);
            session(['wishlist' => array_values($wishlist)]);
        }

        return back()->with('success', 'Product removed from wishlist.');
    }
}
