@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
    {{ session('success') }}
</div>
<meta http-equiv="refresh" content="2">
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">My Wishlist</h1>

    @if($products->isEmpty())
        <div class="text-center text-gray-500 dark:text-gray-300 text-lg py-10">
            Your wishlist is empty.
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

                    @if($product->has_discount)
                        <p class="text-sm text-red-600 font-semibold mt-2">Discount: -{{ number_format($product->discount, 2) }}€</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">From {{ $product->discount_min_qty }} unit(s)</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-gray-500 dark:text-gray-400 line-through">{{ number_format($product->price, 2) }}€</span>
                            <span class="text-sky-600 dark:text-sky-300 font-bold">{{ number_format($product->discounted_price, 2) }}€</span>
                        </div>
                    @else
                        <p class="mt-2 text-lg font-bold text-sky-600 dark:text-sky-300">{{ number_format($product->price, 2) }}€</p>
                    @endif

                    <form method="POST" action="{{ route('wishlist.remove') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button class="w-full text-red-600 hover:underline">Remove from wishlist</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
