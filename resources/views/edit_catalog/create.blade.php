@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">New Product</h1>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input type="text" name="name" id="name" required value="{{ old('name', optional($product)->name) }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>


            <div>
                <label for="category_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category_id" id="category_id"
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    <option value="" disabled {{ old('category_id', optional($product)->category_id) ? '' : 'selected' }}>
                        Select category
                    </option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', optional($product)->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="price" class="text-sm font-medium text-gray-700 dark:text-gray-300">Price (€)</label>
                <input type="number" step="0.01" name="price" id="price" required value="{{ old('price', optional($product)->price) }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock</label>
                <input type="number" name="stock" id="stock" required value="{{ old('stock', optional($product)->stock) }}"
                    class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" id="description" rows="3"
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">{{ old('description', optional($product)->description) }}</textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Upload e visualização da imagem atual  -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                <input type="file" name="photo" id="photo" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                @if(optional($product)->photo)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Current Image:</p>
                    <img src="{{ asset('storage/products/' . $product->photo) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded shadow">
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="discount_min_qty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Quantity for Discount</label>
                    <input type="number" name="discount_min_qty" id="discount_min_qty"
                        value="{{ old('discount_min_qty', optional($product)->discount_min_qty) }}"
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white"
                        min="0">
                    @error('discount_min_qty') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="discount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount Value (€)</label>
                    <input type="number" step="0.01" name="discount" id="discount"
                        value="{{ old('discount', optional($product)->discount) }}"
                        class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white"
                        min="0">
                    @error('discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

             <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="stock_lower_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Lower Limit</label>
                    <input type="number" name="stock_lower_limit" id="stock_lower_limit" required  value="{{ old('stock_lower_limit', optional($product)->stock_lower_limit) }}" class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    @error('stock_lower_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="stock_upper_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Upper Limit</label>
                    <input type="number" name="stock_upper_limit" id="stock_upper_limit" required  value="{{ old('stock_upper_limit', optional($product)->stock_upper_limit) }}" class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                    @error('stock_upper_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4 flex justify-between">
                <button type="submit" class="bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">Create Product</button>
                <a href="{{ route('products.index') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
