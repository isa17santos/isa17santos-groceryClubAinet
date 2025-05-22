<div class="max-w-md mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Login</h1>
    
    @if ($errorMessage)
        <div wire:poll.2s="$set('errorMessage', null)" class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ $errorMessage }}
        </div>
    @endif


    <form wire:submit.prevent="login" class="space-y-6">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" type="email" wire:model.defer="email" required autofocus
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <input id="password" type="password" wire:model.defer="password" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Log in
            </button>
        </div>
    </form>

    <!-- Forgot password -->
    <div class="text-center mt-6 mb-2">
        <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:underline">Forgot password?</a>
    </div>

    <p class="text-sm text-center mt-4 text-gray-500 dark:text-gray-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register</a>
    </p>
</div>

