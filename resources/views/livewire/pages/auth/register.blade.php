<div class="max-w-xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Register</h1>

    @error('general')
        <div class="text-red-600 text-center font-semibold mb-4">{{ $message }}</div>
    @enderror

    <form wire:submit.prevent="register" enctype="multipart/form-data" class="space-y-6">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" wire:model.defer="name" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <select wire:model.defer="gender"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">-- Select Gender --</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" wire:model.defer="email" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <input type="password" wire:model.defer="password" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery address (optional)</label>
            <textarea wire:model.defer="delivery_address"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500"></textarea>
            @error('delivery_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIF (opcional)</label>
            <input type="text" wire:model.defer="nif" maxlength="9" inputmode="numeric"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500" />
            @error('nif') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Payment Method (opcional)</label>
            <select wire:model.defer="payment_type"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">-- Select --</option>
                <option value="Visa">Visa</option>
                <option value="PayPal">PayPal</option>
                <option value="MB WAY">MB WAY</option>
            </select>
            @error('payment_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reference (opcional)</label>
            <input type="text" wire:model.defer="payment_reference"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500" />
            @error('payment_reference') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo (optional)</label>

            @php
                $defaultPhoto = asset('storage/users/anonymous.png');
            @endphp

            <div x-data="{ photoPreview: '{{ $defaultPhoto }}' }" class="flex flex-col items-start">
                <img :src="photoPreview" alt="Photo Preview"
                    class="w-32 h-32 rounded-full object-cover border border-gray-300 dark:border-gray-600 mb-2">

                <input type="file" wire:model="photo" accept="image/*"
                    @change="photoPreview = URL.createObjectURL($event.target.files[0])"
                    class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                @error('photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Register
            </button>
        </div>
    </form>

    @if ($showMessage)
        <div class="text-green-600 text-center font-semibold mb-4">
            Registration completed successfully. Check your email to activate your account.
        </div>
    @endif

    <p class="text-sm text-center mt-6 text-gray-500 dark:text-gray-400">
        Already registered?
        <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login here</a>
    </p>
</div>
