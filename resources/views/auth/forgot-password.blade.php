@extends('layouts.app') 

@section('content')
    <div class="max-w-md mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
        <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Recover Password</h1>
   
        @if (session('status'))
            <div class="mb-4 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" required autofocus
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="text-center mt-4 mb-2">
                <button type="submit"
                    class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                    Send recovery link
                </button>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:underline">Back to Login</a>
            </div>
        </form>
    </div>
@endsection
