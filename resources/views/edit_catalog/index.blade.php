@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Products</h1>

    <a href="{{ route('products.create') }}" class="inline-block mb-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
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
                    <td class="py-4">{{ $product->category->name }}</td>
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
</div>
@endsection





