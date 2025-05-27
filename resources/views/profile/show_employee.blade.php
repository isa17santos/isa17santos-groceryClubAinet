@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">My Profile</h1>

    <div class="space-y-4">
        {{-- Photo --}}
        <div class="flex items-center gap-4">
            <img src="{{ $user->profile_image_url }}" alt="Profile photo" class="w-20 h-20 rounded-full object-cover border border-gray-300">
            <p class="text-sm text-gray-500 dark:text-gray-300">Profile Photo</p>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->name }}</p>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->email }}</p>
        </div>

        {{-- Gender --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $user->gender === 'M' ? 'Male' : 'Female' }}</p>
        </div>

        {{-- Change password --}}
        <div class="mt-6 text-center">
            <a href="{{ route('changePassword') }}"
               class="inline-block bg-lime-600 text-white px-4 py-2 rounded-md hover:bg-lime-700 transition">
                🔑 Change Password
            </a>
        </div>
    </div>
</div>
@endsection
