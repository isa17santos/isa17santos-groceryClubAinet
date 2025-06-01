@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md">
        <ul class="list-disc ml-4">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    <meta http-equiv="refresh" content="5">
@endif
<div class="max-w-4xl mx-auto mt-16 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow space-y-6">
    <h1 class="text-2xl font-bold text-yellow-700">Create Supply Orders</h1>

    <form method="POST" action="{{ route('supply_orders.store') }}" class="space-y-4">
        @csrf

        @php
            $orders = old('orders');
            if (empty($orders)) {
                $orders = [['product_id' => '', 'quantity' => '']];
            }
        @endphp


        @foreach($orders as $i => $order)
            <div class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product</label>
                    <select name="orders[{{ $i }}][product_id]"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500 mx-auto">
                        <option value="">Select...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected($order['product_id'] == $product->id)>
                                {{ $product->name }} (stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                    <input type="number" name="orders[{{ $i }}][quantity]" min="1"
                        value="{{ $order['quantity'] }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500 mx-auto">
                </div>

                @if(count($orders) > 1)
                    <button type="submit" name="remove_row" value="{{ $i }}"
                        class="text-red-600 hover:underline text-sm mt-6">
                        Remove
                    </button>
                @endif
            </div>
        @endforeach

        <div class="flex space-x-4">
            <button type="submit" name="add_row" value="1"
                class="bg-amber-500 text-white py-2 px-5 rounded-md hover:bg-amber-600 transition">
                Add another order
            </button>
        </div>

        <div class="mt-6 flex justify-start space-x-4">
            <button type="submit" name="submit" value="1"
                class="bg-lime-600 text-white py-2 px-5 rounded-md hover:bg-lime-700 transition">
                Submit Supply Order
            </button>
            <a href="{{ route('supply_orders.index') }}" class="text-red-600 hover:underline mt-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
