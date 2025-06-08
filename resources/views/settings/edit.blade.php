@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Edit Membership Fee</h1>

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
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="membership_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Membership Fee (€)</label>
                <input type="number" step="0.01" name="membership_fee" id="membership_fee"
                       value="{{ old('membership_fee', $setting->membership_fee) }}" required
                       class="w-full min-w-0 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
                @error('membership_fee') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex justify-between">
                <button type="submit" class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700">Save</button>
                <a href="{{ route('catalog') }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection