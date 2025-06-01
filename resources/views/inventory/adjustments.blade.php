@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-16 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
    <div class="mt-1 mb-6 text-left">
        <a href="{{ route('inventory.index') }}" 
           class="inline-block text-lime-700 hover:underline dark:text-lime-400">← Back to Inventory</a>
    </div>
    <h1 class="text-3xl font-bold text-yellow-700 dark:text-yellow-700 mb-10 text-center">Stock Adjustment History</h1>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 border-separate border-spacing-y-3">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">Changed By</th>
                    <th class="px-4 py-2 text-left">Change</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($adjustments as $adjustment)
                    <tr class="bg-white dark:bg-gray-700 shadow rounded-lg">
                        <td class="px-4 py-2">{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">{{ $adjustment->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $adjustment->user->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2">
                            @if ($adjustment->quantity_changed > 0)
                                <span class="text-green-600 font-semibold">+{{ $adjustment->quantity_changed }}</span>
                            @else
                                <span class="text-red-600 font-semibold">{{ $adjustment->quantity_changed }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">No stock adjustments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $adjustments->links('vendor.pagination.tailwind-dark') }}
    </div>
</div>
@endsection
