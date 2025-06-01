@extends('layouts.app')

@section('content')
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
            class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
        {{ session('success') }}
    </div>
    <meta http-equiv="refresh" content="5">
@endif
<div class="max-w-6xl mx-auto mt-16 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow">
    <a href="{{ route('inventory.index') }}"
        class="inline-block mb-6 ml-4  text-sm text-lime-700 dark:text-lime-400 hover:underline">
        ← Back to Inventory
    </a>

    <div class="mb-8 relative flex items-center justify-center">
        <h1 class="text-3xl font-bold text-yellow-700 dark:text-yellow-700">Supply Orders</h1>
    </div>

     <div class="flex justify-center gap-16 flex-wrap">
        <a href="{{ route('supply_orders.create') }}">
            <button class="w-full bg-lime-600 text-white py-2 px-5 rounded-md hover:bg-lime-700 transition">
                Manual Order
            </button>
        </a>
        <form method="POST" action="{{ route('supply_orders.auto') }}" class="inline">
            @csrf
            <button type="submit" class="w-full bg-lime-600 text-white py-2 px-5 rounded-md hover:bg-lime-700 transition">
                Generate Automatically
            </button>
        </form>
    </div>

    <div class="overflow-x-auto ml-6 mr-6 mt-8">
        <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 border-separate border-spacing-y-3">
            <thead>
                <tr>
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Registered By</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="bg-white dark:bg-gray-700 shadow rounded-lg">
                        <td class="px-4 py-2 font-medium">
                            {{ $order->product?->name ?? 'Product not found' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $order->quantity }}
                        </td>
                        <td class="px-4 py-2">
                            @if($order->status === 'requested')
                                <span class="text-yellow-500 font-semibold">Requested</span>
                            @else
                                <span class="text-green-600 font-medium">Completed</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            {{ $order->user?->name ?? 'User not found' }}
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            @if($order->status === 'requested')
                                <form method="POST" action="{{ route('supply_orders.complete', $order) }}" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline">Complete</button>
                                </form>
                                <form method="POST" action="{{ route('supply_orders.destroy', $order) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @else
                                <span class="text-gray-400 italic">No actions</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">
                            No supply orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        <div class="pagination w-full flex justify-center">
            {{ $orders->links('vendor.pagination.tailwind-dark') }}
        </div>
    </div>

</div>
@endsection
