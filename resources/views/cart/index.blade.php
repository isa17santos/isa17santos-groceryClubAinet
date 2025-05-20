@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-8 p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold text-green-700 mb-4">Your Shopping Cart</h1>

    @if(empty($cart))
        <p class="text-gray-600">Your cart is empty.</p>
    @else
        <ul class="space-y-4">
            @foreach($cart as $productId => $item)
                <li class="border-b pb-2">
                    Product ID: {{ $productId }}<br>
                    Quantity: {{ $item['quantity'] }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
