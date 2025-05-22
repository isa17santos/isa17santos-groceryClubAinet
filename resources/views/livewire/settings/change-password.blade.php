<div class="max-w-md mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-yellow-700 dark:text-yellow-700 text-center mb-6">Change Password</h1>

    @if ($successMessage)
        <div wire:poll.2s="$set('successMessage', null)" class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ $successMessage }}
        </div>
    @endif

    @if ($errorMessage)
        <div wire:poll.2s="$set('errorMessage', null)" class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="changePassword" class="space-y-6">
        <!-- Current Password -->
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
            <input type="password" id="current_password" wire:model.defer="current_password" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
            <input type="password" id="new_password" wire:model.defer="new_password" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('new_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Confirm New Password -->
        <div>
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" wire:model.defer="new_password_confirmation" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Update Password
            </button>
        </div>
    </form>
</div>
