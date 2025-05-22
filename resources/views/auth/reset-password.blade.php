@extends('layouts.app')

@section('content')

<div class="max-w-md mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-yellow-700 dark:text-yellow-700 text-center mb-6">Reset Password</h1>

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
             class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('status')}}
        </div>
        <meta http-equiv="refresh" content="2">
    @endif
    
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <meta http-equiv="refresh" content="2">
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email (oculto) -->
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <!-- New Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
            <input type="password" id="password" name="password" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <!-- Confirm New Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Reset Password
            </button>
        </div>
    </form>
</div>

@endsection