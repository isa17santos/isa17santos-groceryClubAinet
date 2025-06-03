@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Categories</h1>

    <a href="{{ route('categories.create') }}" class="inline-block mb-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
        New Category
    </a>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @elseif(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
        <meta http-equiv="refresh" content="2">
    @endif

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        <table class="w-full text-left">
            <thead>
                <tr class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b">
                    <th class="pb-3">Name</th>
                    <th class="pb-3">Image</th>
                    <th class="pb-3">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700 dark:text-gray-200">
                @forelse ($categories as $category)
                <tr class="border-t border-gray-200 dark:border-gray-600">
                    <td class="py-4">{{ $category->name }}</td>
                <td class="py-4">
                    <img src="{{ $category->image ? asset('storage/categories/' . $category->image) : asset('storage/categories/category_no_image.png') }}"
                        alt="{{ $category->name }}"
                        class="w-24 h-24 object-cover rounded">
                </td>
                    <td class="py-4 flex items-center space-x-2">
                        <a href="{{ route('categories.edit', $category) }}" class="text-green-700 dark:text-green-300">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-gray-400">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
