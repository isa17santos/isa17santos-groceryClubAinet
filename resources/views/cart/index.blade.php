@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Shopping Cart</h1>

    @if(session('success') && !str_contains(session('success'), 'updated'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
        <meta http-equiv="refresh" content="3">
    @elseif(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @endif

    @if(count($cartItems) > 0)
    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b">
                    <th class="pb-3">Product</th>
                    <th class="pb-3">Unit Price</th>
                    <th class="pb-3">Quantity</th>
                    <th class="pb-3">Subtotal</th>
                    <th class="pb-3"></th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-gray-200">
                @foreach($cartItems as $item)
                <tr class="border-t border-gray-200 dark:border-gray-600">
                    <td class="py-4 flex items-center gap-4">
                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" class="w-17 h-16 object-cover rounded">
                        <div>
                            <div class="font-medium">{{ $item['product']->name }}</div>
                            @if($item['product']->stock < $item['quantity'])
                                <div class="text-red-500 text-sm">Insufficient stock - delivery may be delayed</div>
                            @endif
                        </div>
                    </td>
                    <td class="py-4">
                        @if($item['product']->has_discount && $item['quantity'] >= $item['product']->discount_min_qty)
                            <span class="text-gray-500 line-through">{{ number_format($item['product']->price, 2) }}€</span>
                            <span class="text-sky-600 font-bold ml-1">{{ number_format($item['price'], 2) }}€</span>
                        @else
                            {{ number_format($item['price'], 2) }}€
                        @endif
                    </td>
                    <td class="py-4">
                        <form method="POST" action="{{ route('cart.update') }}" class="flex gap-2 items-center" onchange="this.submit()">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0"
                                   class="w-16 rounded-md border border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:border-gray-600 bg-white dark:bg-gray-700 text-center">
                        </form>
                    </td>
                    <td class="py-4">{{ number_format($item['subtotal'], 2) }}€</td>
                    <td class="py-4">
                        <form method="POST" action="{{ route('cart.remove') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                            <button class="text-red-600 hover:underline">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 mb-4">
        <a href="{{ route('catalog') }}">
            <button type="button" class="w-full bg-lime-500 text-white py-2 rounded-md hover:bg-lime-600 transition">
                Add Product
            </button>
        </a>
    </div>

    <form method="POST" action="{{ route('cart.clear') }}">
        @csrf
        <button class="mb-4 text-red-500 hover:underline">Clear Cart</button>
    </form>

    <!-- Paginação -->
    <div class="mt-6 flex justify-center">
        {{ $cartItems->links('vendor.pagination.tailwind-dark') }}
    </div>

    <div class="flex justify-between items-start flex-col md:flex-row gap-6">
        
        <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow w-full">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Products total: {{ number_format($total, 2) }}€</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Shipping: {{ number_format($shipping, 2) }}€</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">Total: {{ number_format($finalTotal, 2) }}€</p>

            @if ($errors->any())
                <div class="mt-4 mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('cart.checkout') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Address</label>
                    <input type="text" name="delivery_address" id="delivery_address" value="{{ old('delivery_address', auth()->user()->default_delivery_address ?? '') }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:border-gray-600 bg-white dark:bg-gray-700">
                </div>
                <div>
                    <label for="nif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIF</label>
                    <input type="text" name="nif" id="nif" value="{{ old('nif', auth()->user()->nif ?? '') }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:border-gray-600 bg-white dark:bg-gray-700">
                </div>
                <button type="submit" class="w-full bg-lime-500 text-white py-2 rounded-md hover:bg-lime-600 transition">
                    Confirm Purchase
                </button>
            </form>
        </div>
    </div>

    @else
        <div class="text-center text-gray-500 dark:text-gray-300 text-lg py-10">
            Your cart is empty.
        </div>
    @endif
</div>
@endsection
