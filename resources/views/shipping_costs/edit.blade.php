@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Edit Shipping Cost</h1>

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
        <form method="POST" action="{{ route('shipping-costs.update', $shippingCost) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('shipping_costs.partials.form')

            <div class="pt-4 flex justify-between">
                <button type="submit" class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700">Update</button>
                <a href="{{ route('shipping-costs.index') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
