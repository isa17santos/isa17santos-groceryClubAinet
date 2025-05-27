@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">My Profile</h1>

    <div class="space-y-4">
        <div class="flex items-center gap-4">
            <img src="{{ $user->profile_image_url }}" alt="Profile photo" class="w-20 h-20 rounded-full object-cover border border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->email }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">
                {{ $user->gender === 'M' ? 'Male' : 'Female' }}
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Address</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->default_delivery_address ?? '—' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIF</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->nif ?? '—' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->default_payment_type ?? '—' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reference</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->default_payment_reference ?? '—' }}</p>
        </div>

        <div class="mt-6 text-center flex flex-col gap-4 sm:flex-row justify-center">
            <a href="{{ route('profile.edit', $user) }}"
               class="bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">
                ✏️ Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
