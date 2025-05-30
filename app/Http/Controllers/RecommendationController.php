<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = session('wishlist', []);
        $cart = session('cart', []);
        $cartProductIds = array_keys($cart);
        $sessionProductIds = array_map('intval', array_merge($wishlist, $cartProductIds));

        $user = Auth::user();
        $custom = $user->custom ?? [];

        if (is_string($custom)) {
            $custom = json_decode($custom, true);
        }

        $likes = collect($custom['product_feedback'] ?? [])
            ->filter(fn($v) => $v === 'like')
            ->keys()
            ->map(fn($id) => (int) $id)
            ->all();

        $dislikes = collect($custom['product_feedback'] ?? [])
            ->filter(fn($v) => $v === 'dislike')
            ->keys()
            ->map(fn($id) => (int) $id)
            ->all();

        $dislikedInSession = array_intersect($dislikes, $sessionProductIds);
        $dislikedToExclude = array_diff($dislikes, $dislikedInSession);

        // Categorias dos produtos no carrinho/wishlist
        $categoryIdsSession = Product::whereIn('id', $sessionProductIds)->pluck('category_id')->unique();

        // Categorias dos produtos com like
        $categoryIdsLikes = Product::whereIn('id', $likes)->pluck('category_id')->unique();

        // Categorias para mostrar nos filtros
        $allCategoryIds = $categoryIdsSession->merge($categoryIdsLikes)->unique();
        $availableCategories = Category::whereIn('id', $allCategoryIds)->get();

        // Obter IDs de produtos recomendados via categorias (sem dislikes)
        $productsFromCategoriesIds = Product::whereIn('category_id', $categoryIdsSession)
            ->whereNotIn('id', $dislikedToExclude)
            ->pluck('id')
            ->all();

        // Obter IDs de produtos com like (sem dislikes)
        $likedProductsIds = Product::whereIn('id', $likes)
            ->whereNotIn('id', $dislikedToExclude)
            ->pluck('id')
            ->all();

        // IDs finais dos produtos a mostrar
        $allProductIds = array_unique(array_merge($productsFromCategoriesIds, $likedProductsIds));

        if (empty($allProductIds)) {
            return view('recommended.index', [
                'products' => collect(),
                'filtersApplied' => false,
                'availableCategories' => $availableCategories,
                'showFilters' => true
            ]);
        }

        // Query final com todos os produtos recomendados
        $query = Product::whereIn('id', $allProductIds);
        $this->applyFilters($query, $request);

        $filteredProducts = $query->get();

        // Ordenação manual se necessário (caso sort esteja definido)
        $sort = $request->input('sort');
        if ($sort === 'name_asc') {
            $filteredProducts = $filteredProducts->sortBy('name')->values();
        } elseif ($sort === 'price_asc') {
            $filteredProducts = $filteredProducts->sortBy('price')->values();
        } elseif ($sort === 'price_desc') {
            $filteredProducts = $filteredProducts->sortByDesc('price')->values();
        }

        // Paginação manual
        $perPage = 12;
        $page = $request->get('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredProducts->forPage($page, $perPage),
            $filteredProducts->count(),
            $perPage,
            $page,
            ['path' => route('recommended'), 'query' => $request->query()]
        );

        $filtersApplied = $request->hasAny([
            'name', 'min_price', 'max_price', 'in_stock', 'on_sale', 'sort', 'category'
        ]);

        return view('recommended.index', [
            'products' => $paginated,
            'filtersApplied' => $filtersApplied,
            'availableCategories' => $availableCategories,
            'showFilters' => true
        ]);
    }


    protected function applyFilters(&$query, Request $request)
    {
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->has('on_sale')) {
            $query->whereNotNull('discount')->where('discount', '>', 0);
        }
    }



    public function storeFeedback(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'feedback' => 'required|in:like,dislike',
        ]);

        $user = Auth::user();

        $custom = $user->custom ?? [];

        if (is_string($custom)) {
            $custom = json_decode($custom, true);
        }

        $custom['product_feedback'][$request->product_id] = $request->feedback;

        $user->custom = $custom;
        $user->save();

        $message = $request->feedback === 'like' ? 'You liked this product.' : 'You disliked this product.';

        return back()->with('feedback_message', $message)
                    ->with('feedback_type', $request->feedback);
    }

}
