<div>
    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
    <input type="text" name="name" id="name" required value="{{ old('name', optional($product)->name) }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
    <select name="category_id" id="category_id"
            class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', optional($product)->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (€)</label>
    <input type="number" step="0.01" name="price" id="price" required value="{{ old('price', optional($product)->price) }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
    @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock</label>
    <input type="number" name="stock" id="stock" required value="{{ old('stock', optional($product)->stock) }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
    @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
    <textarea name="description" id="description" rows="3"
              class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">{{ old('description', optional($product)->description) }}</textarea>
    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div>
    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Photo (file name)</label>
    <input type="text" name="photo" id="photo" value="{{ old('photo', optional($product)->photo) }}"
           class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
    @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="discount_min_qty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount Min Qty</label>
        <input type="number" name="discount_min_qty" id="discount_min_qty" value="{{ old('discount_min_qty', optional($product)->discount_min_qty) }}"
               class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
        @error('discount_min_qty') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="discount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount (€)</label>
        <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', optional($product)->discount) }}"
               class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
        @error('discount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="stock_lower_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Stock</label>
        <input type="number" name="stock_lower_limit" id="stock_lower_limit" required value="{{ old('stock_lower_limit', optional($product)->stock_lower_limit) }}"
               class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
        @error('stock_lower_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="stock_upper_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Stock</label>
        <input type="number" name="stock_upper_limit" id="stock_upper_limit" required value="{{ old('stock_upper_limit', optional($product)->stock_upper_limit) }}"
               class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
        @error('stock_upper_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
