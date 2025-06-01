@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-16 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow">
    <div class="mt-8 mb-10 relative flex items-center justify-center">
        <h1 class="text-3xl font-bold text-yellow-700 dark:text-yellow-700">Inventory</h1>
        
    </div>

    <div class="flex justify-between items-center flex-wrap gap-4 mb-10">
        <div class="flex-shrink-0">
            <a href="{{ route('supply_orders.index') }}">
                <button type="button" class="bg-lime-600 text-white py-2 px-4 rounded-md hover:bg-lime-600 transition">
                    Supply Order
                </button>
            </a>
        </div>

        <form method="GET" class="flex flex-col items-center">
            <label for="filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by stock:</label>
            <select name="filter" id="filter" onchange="this.form.submit()" class="w-48 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500 mx-auto">
                <option value="">All</option>
                <option value="low" @selected($filter === 'low')>Low Stock</option>
                <option value="out" @selected($filter === 'out')>Out of Stock</option>
            </select>
        </form>

        <div class="flex-shrink-0">
            <a href="{{ route('inventory.adjustments') }}">
                <button type="button" class="bg-lime-600 text-white py-2 px-4 rounded-md hover:bg-lime-600 transition">
                    Adjustment History
                </button>
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 border-separate border-spacing-y-3">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Stock</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="bg-white dark:bg-gray-700 shadow rounded-lg">
                        <td class="px-4 py-2 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-2">{{ $product->stock }}</td>
                        <td class="px-4 py-2">
                            @if ($product->stock <= 0)
                                <span class="text-red-600 font-semibold">Out of stock</span>
                            @elseif ($product->stock < $product->stock_lower_limit)
                                <span class="text-yellow-500 font-semibold">Low</span>
                            @else
                                <span class="text-green-600 font-medium">OK</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('inventory.adjust', $product) }}"
                               class="text-green-600 hover:underline dark:text-green-400">Adjust</a>
                        </td>
                    </tr>
                @endforeach
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
