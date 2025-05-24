@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <a href="{{ route('card.show') }}"
        class="inline-block mb-6 text-sm text-lime-700 dark:text-lime-400 hover:underline">
        ← Back to Card
    </a>

    <h1 class="text-4xl font-bold text-yellow-700 dark:text-yellow-700 mb-6">Order #{{ $order->id }} Details</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
        <h2 class="px-10 text-2xl font-semibold text-lime-700 dark:text-lime-400 mb-4">Products</h2>

        <table class="w-full text-left text-sm">
            <thead>
                <tr class="px-10 border-b border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300">
                    <th class="px-12 pb-3">Image</th>
                    <th class="pb-3">Name</th>
                    <th class="pb-3">Unit Price</th>
                    <th class="pb-3">Quantity</th>
                    <th class="pb-3">Subtotal</th>
                </tr>
            </thead>
            <tbody class="text-gray-800 dark:text-white">
                @foreach($products as $product)
                <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td class="px-10 py-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="py-3">{{ $product->name }}</td>
                    <td class="px-3 py-3">
                        @if($product->pivot->discount > 0)
                            <span class="text-gray-500 line-through">{{ number_format($product->pivot->unit_price + $product->pivot->discount, 2) }}€</span>
                            <span class="ml-1 text-sky-600 font-bold">{{ number_format($product->pivot->unit_price, 2) }}€</span>
                        @else
                            {{ number_format($product->pivot->unit_price, 2) }}€
                        @endif
                    </td>
                    <td class="px-6 py-3">{{ $product->pivot->quantity }}</td>
                    <td class="px-3 py-3 font-semibold">{{ number_format($product->pivot->subtotal, 2) }}€</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $products->links('vendor.pagination.tailwind-dark') }}
        </div>

    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-lime-700 dark:text-lime-400 mb-4">Order Summary</h2>
        <p><strong>Products Total:</strong> {{ number_format($order->total - $order->shipping_cost, 2) }}€</p>
        <p><strong>Shipping Cost:</strong> {{ number_format($order->shipping_cost, 2) }}€</p>
        <p><strong>Total:</strong> {{ number_format($order->total, 2) }}€</p>
        <p class="text-gray-700 dark:text-gray-300"><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</p>
        <p class="text-gray-700 dark:text-gray-300"><strong>Status:</strong>
            @if($order->status === 'completed')
                <span class="text-green-600 dark:text-green-400">Completed</span>
            @elseif($order->status === 'pending')
                <span class="text-yellow-600 dark:text-yellow-400">Pending</span>
            @elseif($order->status === 'canceled')
                <span class="text-red-600 dark:text-red-400">Canceled</span>
            @else
                {{ ucfirst($order->status) }}
            @endif
        </p>
        <p><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
        @if($order->nif)
            <p><strong>NIF:</strong> {{ $order->nif }}</p>
        @endif

        @if($order->pdf_receipt)
        <div class="mt-4">
            <a href="{{ route('receipt.download', $order->id) }}" target="_blank"
               class="text-blue-600 hover:underline">📄 Download Receipt</a>
        </div>
        @endif
    </div>
</div>
@endsection
