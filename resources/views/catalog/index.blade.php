@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md shadow-sm">
    {{ session('success') }}
</div>
<meta http-equiv="refresh" content="3">
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 mb-10">Product Catalog</h1>

    <form method="GET" action="{{ route('catalog') }}" class="mb-8 bg-white p-5 rounded-lg shadow flex flex-col lg:flex-row lg:items-end gap-6 w-full">

        <!-- Categoria -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="category" class="text-sm font-medium text-gray-700">Category</label>
            <select name="category" id="category"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">All</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Nome -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="name" class="text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ request('name') }}"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500"
                placeholder="Search...">
        </div>

        <!-- Preço mínimo -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="min_price" class="text-sm font-medium text-gray-700">Minimum price (€)</label>
            <input type="number" step="0.01" name="min_price" id="min_price" value="{{ request('min_price') }}"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <!-- Preço máximo -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="max_price" class="text-sm font-medium text-gray-700">Maximum price (€)</label>
            <input type="number" step="0.01" name="max_price" id="max_price" value="{{ request('max_price') }}"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <!-- Em stock -->
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="in_stock" id="in_stock" {{ request()->has('in_stock') ? 'checked' : '' }}>
            <label for="in_stock" class="text-sm text-gray-700">Only in stock</label>
        </div>

        <!-- Com desconto -->
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="on_sale" id="on_sale" {{ request()->has('on_sale') ? 'checked' : '' }}>
            <label for="on_sale" class="text-sm text-gray-700">Only with discount</label>
        </div>

        <!-- Ordenação -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="sort" class="text-sm font-medium text-gray-700">Order by</label>
            <select name="sort" id="sort"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500">
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

        <!-- Botão limpar -->
        @if(
        request()->filled('category') ||
        request()->filled('name') ||
        request()->filled('min_price') ||
        request()->filled('max_price') ||
        request()->input('in_stock') !== null ||
        request()->input('on_sale') !== null ||
        request()->filled('sort')
        )
        <div class="mt-2">
            <a href="{{ route('catalog') }}"
                class="text-sm text-red-500 underline hover:text-red-700">Clear filter</a>
        </div>
        @endif
    </form>


    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($products as $product)
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden hover:shadow-2xl transition
                    @if($product->has_discount) border-2 border-yellow-500 @endif">

            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">

            <div class="p-4">
                <h2 class="text-xl font-bold text-lime-600 mt-2">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                <p class="text-sm text-gray-700 mt-1 line-clamp-3">{{ $product->description }}</p>

                @if($product->has_discount)
                <p class="text-sm text-red-600 font-semibold mt-2">Discount: -{{ number_format($product->discount, 2) }}€</p>
                <p class="text-sm text-gray-600">From {{ $product->discount_min_qty }} unit(s)</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-gray-500 line-through">{{ number_format($product->price, 2) }}€</span>
                    <span class="text-sky-600 font-bold">{{ number_format($product->discounted_price, 2) }}€</span>
                </div>
                @else
                <p class="mt-2 text-lg font-bold text-sky-600">{{ number_format($product->price, 2) }}€</p>
                @endif

                <form method="POST" action="{{ route('cart.add') }}" class="mt-4 flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <input
                        type="number"
                        name="quantity"
                        id="quantity-{{ $product->id }}"
                        value="1"
                        min="1"
                        step="1"
                        onkeydown="return false"
                        class="w-20 rounded-l-md border border-gray-300 shadow-sm focus:ring-lime-600 focus:border-lime-600 text-center h-10">

                    <button
                        type="submit"
                        class="h-10 bg-lime-600 text-white px-4 rounded-r-md hover:bg-lime-700 transition flex items-center justify-center">
                        Add
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-600 text-lg py-10">
            No products found matching your filters.
        </div>
        @endforelse
    </div>
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
</div>
@endsection