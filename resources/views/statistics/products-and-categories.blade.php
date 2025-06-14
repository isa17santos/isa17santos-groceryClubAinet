@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('statistics.index') }}" class="inline-flex items-center text-lime-700 hover:underline dark:text-lime-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            ← Back to Statistics
        </a>

        <a href="{{ route('statistics.export.products-and-categories') }}"
           class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700">
            Export CSV
        </a>
    </div>

    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Totals of Products and Categories</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="text-lg font-semibold text-gray-700 dark:text-gray-300 border-b">
                    <th class="pb-3">Category</th>
                    <th class="pb-3 text-right">Number of Products</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-200">
                @foreach ($productsPerCategory as $category)
                    <tr class="border-b border-gray-200 dark:border-gray-600">
                        <td class="py-4">{{ $category->name }}</td>
                        <td class="py-4 text-right font-semibold">{{ $category->products_count }}</td>
                    </tr>
                @endforeach
                <tr class="border-t font-bold text-gray-800 dark:text-gray-100">
                    <td class="py-4">Total Categories</td>
                    <td class="py-4 text-right">{{ $totalCategories }}</td>
                </tr>
                <tr class="border-t font-bold text-gray-800 dark:text-gray-100">
                    <td class="py-4">Total Products</td>
                    <td class="py-4 text-right">{{ $totalProducts }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
