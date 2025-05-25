<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = session('wishlist', []);
        $cart = session('cart', []);
        $cartProductIds = array_keys($cart);
        $productIds = array_merge($wishlist, $cartProductIds);

        // Verifica se há produtos recomendados
        $hasRecommendations = !empty($productIds);

        if (!$hasRecommendations) {
            return view('recommended.index', [
                'products' => collect(),
                'filtersApplied' => false,
                'availableCategories' => collect(),
                'showFilters' => false
            ]);
        }

        $categoryIds = Product::whereIn('id', $productIds)->pluck('category_id')->unique();
        $availableCategories = Category::whereIn('id', $categoryIds)->get();

        $query = Product::whereIn('category_id', $categoryIds);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->has('on_sale')) {
            $query->whereNotNull('discount')->where('discount', '>', 0);
        }

        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
        }

        $products = $query->paginate(12)->appends($request->query());
        $filtersApplied = $request->hasAny(['name', 'min_price', 'max_price', 'in_stock', 'on_sale', 'sort', 'category']);

        return view('recommended.index', [
            'products' => $products,
            'filtersApplied' => $filtersApplied,
            'availableCategories' => $availableCategories,
            'showFilters' => true
        ]);
    }

}
