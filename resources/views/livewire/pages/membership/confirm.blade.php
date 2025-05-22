<div class="max-w-lg mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-yellow-600 mb-6">Confirm Membership</h1>

    @if (session('error'))
        <div class="mb-4 text-red-500">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="pay" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Payment Type</label>
            <select wire:model="payment_type" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                <option value="visa">Visa</option>
                <option value="paypal">PayPal</option>
                <option value="mbway">MB WAY</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Payment Reference</label>
            <input wire:model.defer="payment_reference" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" />
            @error('payment_reference') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
            Pay & Confirm Membership
        </button>
    </form>
</div>
