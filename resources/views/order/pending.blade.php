@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
        {{ session('success') }}
    </div>
    <meta http-equiv="refresh" content="4">
@elseif ($errors->any())
    <div class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ $errors->first() }}
    </div>
    <meta http-equiv="refresh" content="7">
@endif
<div class="max-w-4xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Pending Orders</h1>

    <div class="space-y-4">
        @forelse ($orders as $order)
            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm">
                <div>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">Order #{{ $order->id }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Member: {{ $order->user->name }}</p>
                </div>
                <a href="{{ route('order.pending.details', $order->id) }}" 
                   class="text-sm text-lime-700 dark:text-lime-400 font-medium hover:underline">
                    Details →
                </a>
            </div>
        @empty
            <p class="text-center text-gray-600 dark:text-gray-300">There are no pending orders at the moment.</p>
        @endforelse
    </div>

    <div class="mt-8 flex justify-center">
        <div class="pagination w-full flex justify-center">
            {{ $orders->links('vendor.pagination.tailwind-dark') }}
        </div>
    </div>
</div>
@endsection
