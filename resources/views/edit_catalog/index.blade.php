@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-8">Products</h1>

    <a href="{{ route('products.create') }}" class="inline-block mb-4 bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">
        New Product
    </a>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
            class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
            class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif


    <form method="GET" action="{{ route('products.index') }}" class="mb-8 bg-white dark:bg-gray-800 p-5 rounded-lg shadow flex flex-col lg:flex-row lg:items-end gap-6 w-full">

        <!-- Categoria -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="category" class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
            <select name="category" id="category"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                <option value="">All</option>
                <option value="none" {{ request('category') === 'none' ? 'selected' : '' }}>Sem categoria</option>
                @foreach($categories as $cat)
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
            <label for="min_price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Minimum price (€)</label>
            <input type="number" step="0.01" name="min_price" id="min_price" value="{{ request('min_price') }}"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
        </div>

        <!-- Preço máximo -->
        <div class="flex flex-col flex-1 min-w-0">
            <label for="max_price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Maximum price (€)</label>
            <input type="number" step="0.01" name="max_price" id="max_price" value="{{ request('max_price') }}"
                class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
        </div>

        <!-- Em stock -->
        <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="in_stock" id="in_stock" {{ request()->has('in_stock') ? 'checked' : '' }}>
            <label for="in_stock" class="text-sm text-gray-700 dark:text-gray-300">Only in stock</label>
        </div>

        <!-- Com desconto -->
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
            <a href="{{ route('products.index') }}"
                class="text-sm text-red-500 underline hover:text-red-700">Clear filter</a>
        </div>
        @endif
    </form>

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b">
                    <th class="pb-3 text-center">Image</th> 
                    <th class="pb-3">Name</th>
                    <th class="pb-3">Category</th>
                    <th class="pb-3">Price (€)</th>
                    <th class="pb-3">Stock</th>
                    <th class="pb-3 text-center">Description</th>
                    <th class="pb-3">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-gray-200">
                @forelse ($products as $product)
                <tr class="border-t border-gray-200 dark:border-gray-600">
                    <td class="py-4 text-center">
                        <img src="{{ $product->photo ? asset('storage/products/' . $product->photo) : asset('storage/products/product_no_image.png') }}"
                            alt="{{ $product->name }}"
                            class="w-12 h-12 object-cover rounded">
                    </td> <!-- Adicionado -->
                    <td class="py-4">{{ $product->name }}</td>
                    <td>
                        @if ($product->category && $product->category->trashed())
                            Sem categoria
                        @elseif ($product->category)
                            {{ $product->category->name }}
                        @else
                            Sem categoria
                        @endif
                    </td>
                    <td class="py-4">{{ number_format($product->price, 2) }}€</td>
                    <td class="py-4">{{ $product->stock }}</td>
                    <td class="py-4 text-center">{{ Str::limit($product->description, 50) }}</td>
                    <td class="py-4 flex items-center space-x-2">
                        <a href="{{ route('products.edit', $product) }}" class="text-green-700 dark:text-green-300">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-400">No products found.</td> <!-- Corrigido colspan -->
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-8 flex justify-center">
        <div class="pagination w-full flex justify-center">
            {{ $products->links('vendor.pagination.tailwind-dark') }}
        </div>
    </div>
</div>
@endsection





