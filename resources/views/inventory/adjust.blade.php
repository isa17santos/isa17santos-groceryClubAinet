@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-yellow-700 dark:text-yellow-700 mb-6">Adjust Stock for {{ $product->name }}</h1>

    <form action="{{ route('inventory.adjust.store', $product) }}" method="POST" class="space-y-6">
        @csrf
        @method('POST')

        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Stock</label>
            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $product->stock) }}" 
                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500" />
            @error('quantity')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" 
                class="bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">
            Adjust Stock
        </button>

        <a href="{{ route('inventory.index') }}" 
           class="inline-block ml-5 text-red-700 hover:underline dark:text-red-400">Cancel</a>
    </form>
</div>
@endsection
