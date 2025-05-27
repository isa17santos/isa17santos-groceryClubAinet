<div>
    <label for="min_value_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Value (€)</label>
    <input type="number" step="0.01" name="min_value_threshold" id="min_value_threshold"
           value="{{ old('min_value_threshold', $shippingCost->min_value_threshold ?? '') }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm" required>
    @error('min_value_threshold') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="max_value_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Value (€)</label>
    <input type="number" step="0.01" name="max_value_threshold" id="max_value_threshold"
           value="{{ old('max_value_threshold', $shippingCost->max_value_threshold ?? '') }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm" required>
    @error('max_value_threshold') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="shipping_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Cost (€)</label>
    <input type="number" step="0.01" name="shipping_cost" id="shipping_cost"
           value="{{ old('shipping_cost', $shippingCost->shipping_cost ?? '') }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm" required>
    @error('shipping_cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
