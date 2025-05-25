@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">
        Recommended for you
    </h1>

    <!-- Filtros -->
    @if($showFilters)
        <form method="GET" action="{{ route('recommended') }}"
            class="mb-8 bg-white dark:bg-gray-800 p-5 rounded-lg shadow flex flex-col lg:flex-row lg:items-end gap-6 w-full">

            <!-- Categoria -->
            <div class="flex flex-col flex-1 min-w-0">
                <label for="category" class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category" id="category"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All</option>
                    @foreach($availableCategories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nome -->
            <div class="flex flex-col flex-1 min-w-0">
                <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Search...">
            </div>

            <!-- Preço mínimo -->
            <div class="flex flex-col flex-1 min-w-0">
                <label for="min_price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Min price (€)</label>
                <input type="number" name="min_price" id="min_price" step="0.01" value="{{ request('min_price') }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Preço máximo -->
            <div class="flex flex-col flex-1 min-w-0">
                <label for="max_price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Max price (€)</label>
                <input type="number" name="max_price" id="max_price" step="0.01" value="{{ request('max_price') }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- In stock -->
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="in_stock" id="in_stock" {{ request()->has('in_stock') ? 'checked' : '' }}>
                <label for="in_stock" class="text-sm text-gray-700 dark:text-gray-300">Only in stock</label>
            </div>

            <!-- Em promoção -->
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="on_sale" id="on_sale" {{ request()->has('on_sale') ? 'checked' : '' }}>
                <label for="on_sale" class="text-sm text-gray-700 dark:text-gray-300">Only with discount</label>
            </div>

            <!-- Ordenação -->
            <div class="flex flex-col flex-1 min-w-0">
                <label for="sort" class="text-sm font-medium text-gray-700 dark:text-gray-300">Order by</label>
                <select name="sort" id="sort"
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    <option value="">--</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price ↑</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price ↓</option>
                </select>
            </div>

            <div>
                <button type="submit"
                        class="bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">
                    Filter
                </button>
            </div>

            @if($filtersApplied)
                <div class="mt-2">
                    <a href="{{ route('recommended') }}"
                    class="text-sm text-red-500 underline hover:text-red-700">Clear filters</a>
                </div>
            @endif
        </form>
    @endif
    <!-- Resultados -->
    @if($products->isEmpty())
        <div class="text-center text-gray-500 dark:text-gray-300 text-lg py-10">
            There are no products recommended for you.
        </div>
    @else
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden hover:shadow-2xl transition">
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
                         class="w-full h-48 object-cover">

                    <div class="p-4">
                        <h2 class="text-xl font-bold text-lime-600 dark:text-lime-400 mt-2">{{ $product->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ $product->category->name ?? 'No category' }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200 mt-1 line-clamp-3">{{ $product->description }}</p>

                        @if($product->discount && $product->discount_min_qty)
                            <p class="text-sm text-red-600 font-semibold mt-2">Discount: -{{ number_format($product->discount, 2) }}€</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">From {{ $product->discount_min_qty }} unit(s)</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-500 line-through">{{ number_format($product->price, 2) }}€</span>
                                <span class="text-sky-600 font-bold">{{ number_format($product->price - $product->discount, 2) }}€</span>
                            </div>
                        @else
                            <p class="mt-2 text-lg font-bold text-sky-600">{{ number_format($product->price, 2) }}€</p>
                        @endif
                        <div class="mt-4 flex items-center gap-6">
                            <form method="POST" action="{{ route('cart.add') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="number" name="quantity" id="quantity-{{ $product->id }}" value="1" min="1" step="1" onkeydown="return false"
                                    class="w-20 rounded-l-md border border-gray-300 dark:border-gray-600 shadow-sm focus:ring-lime-600 focus:border-lime-600 text-center h-10 dark:bg-gray-700 dark:text-white">
                                <button type="submit"
                                        class="h-10 bg-lime-600 text-white px-4 rounded-r-md hover:bg-lime-700 transition flex items-center justify-center">
                                    Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $products->links('vendor.pagination.tailwind-dark') }}
        </div>
    @endif
</div>
@endsection
