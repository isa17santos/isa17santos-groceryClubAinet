@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md shadow-sm">
    {{ session('success') }}
</div>
<meta http-equiv="refresh" content="3">
@endif
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 mb-10">Catálogo de Produtos</h1>

    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($products as $product)
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden hover:shadow-2xl transition
                    @if($product->has_discount) border-2 border-yellow-500 @endif">

            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">

            <div class="p-4">
                <h2 class="text-xl font-bold text-lime-600 mt-2">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500">{{ $product->category->name ?? 'Sem categoria' }}</p>
                <p class="text-sm text-gray-700 mt-1 line-clamp-3">{{ $product->description }}</p>

                @if($product->has_discount)
                <p class="text-sm text-red-600 font-semibold mt-2">Desconto: -{{ number_format($product->discount, 2) }}€</p>
                <p class="text-sm text-gray-600">A partir de {{ $product->discount_min_qty }} unidades</p>
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
                        Adicionar
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection