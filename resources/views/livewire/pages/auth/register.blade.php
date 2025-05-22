<div class="max-w-xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Register</h1>

    <form wire:submit.prevent="register" enctype="multipart/form-data" class="space-y-6">

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input id="name" type="text" wire:model.defer="name" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Gender -->
        <div class="mt-4">
            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <select id="gender" wire:model.defer="gender" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">-- Select Gender --</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" type="email" wire:model.defer="email" required
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

        <!-- Address (optional) -->
        <div>
            <label for="delivery_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery address (optional)</label>
            <textarea id="delivery_address" wire:model.defer="delivery_address"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500"></textarea>
            @error('delivery_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- NIF (optional) -->
        <div class="mt-4">
            <label for="nif" class="block text-sm font-medium text-gray-700">NIF (opcional)</label>
            <input id="nif" type="text" wire:model.defer="nif" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500" maxlength="9" inputmode="numeric" />
            @error('nif') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Payment Details -->
        <div class="mt-4">
            <label for="payment_type" class="block text-sm font-medium text-gray-700">Preferred Payment Method (opcional)</label>
            <select id="payment_type" wire:model.defer="payment_type" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">-- Select --</option>
                <option value="Visa">Visa</option>
                <option value="PayPal">PayPal</option>
                <option value="MB WAY">MB WAY</option>
            </select>
            @error('payment_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <label for="payment_reference" class="block text-sm font-medium text-gray-700">Payment Reference (opcional)</label>
            <input id="payment_reference" type="text" wire:model.defer="payment_reference" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500" />
            @error('payment_reference') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Profile Photo (optional) -->
        <div>
            <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profile photo (optional)</label>
            <input id="photo" type="file" wire:model.defer="photo"
                class="mt-1 w-full text-sm text-gray-700 dark:text-gray-200">
            @error('photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Register
            </button>
        </div>
    </form>

    <p class="text-sm text-center mt-6 text-gray-500 dark:text-gray-400">
        Already registered?
        <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login here</a>
    </p>
</div>