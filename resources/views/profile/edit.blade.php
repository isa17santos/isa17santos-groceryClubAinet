@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-16 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow">

    <div class="mb-4 text-left">
        <a href="{{ route('profile.show', $user) }}"
        class="inline-block text-sm text-green-600 dark:text-green-500 hover:underline transition">
            ← Back to Profile
        </a>
    </div>


    <h1 class="text-3xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-6">Edit Profile</h1>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm">
            {{ session('error') }}
        </div>
    @endif


    <form method="POST" action="{{ route('profile.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Profile Photo -->
        <div class="mb-4">
            <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Photo</label>
            
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Foto atual -->
                <div class="flex flex-col items-center">
                    <img src="{{ $user->profile_image_url }}" alt="Profile photo" class="mt-2 w-24 h-24 rounded-full object-cover border border-gray-300">
                </div>

                <!-- Input de nova foto -->
                <div class="flex-1">
                    <input type="file" name="photo" id="photo"
                        class="mt-7 ml-6 w-full text-gray-700 dark:text-white dark:bg-gray-700 focus:outline-none">
                    @error('photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Name  -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Gender -->
        <div class="mb-4">
            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <select name="gender" id="gender"
                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="M" @selected(old('gender', $user->gender) == 'M')>Male</option>
                <option value="F" @selected(old('gender', $user->gender) == 'F')>Female</option>
            </select>
            @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        @if (in_array($user->type, ['member', 'board']))
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Delivery Address -->
            <div class="mb-4">
                <label for="default_delivery_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Address</label>
                <input type="text" name="default_delivery_address" id="default_delivery_address"
                    value="{{ old('default_delivery_address', $user->default_delivery_address) }}"
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                @error('default_delivery_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- NIF -->
            <div class="mb-4">
                <label for="nif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NIF</label>
                <input type="text" name="nif" id="nif" value="{{ old('nif', $user->nif) }}"
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                @error('nif') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Payment Method -->
            <div class="mb-4">
                <label for="default_payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Payment Method</label>
                <select name="default_payment_type" id="default_payment_type"
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                    <option value="">-- Select --</option>
                    <option value="Visa" @selected(old('default_payment_type', $user->default_payment_type) == 'Visa')>Visa</option>
                    <option value="PayPal" @selected(old('default_payment_type', $user->default_payment_type) == 'PayPal')>PayPal</option>
                    <option value="MB WAY" @selected(old('default_payment_type', $user->default_payment_type) == 'MB WAY')>MB WAY</option>
                </select>
                @error('default_payment_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Payment Reference -->
            <div class="mb-4">
                <label for="default_payment_reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reference</label>
                <input type="text" name="default_payment_reference" id="default_payment_reference"
                    value="{{ old('default_payment_reference', $user->default_payment_reference) }}"
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                @error('default_payment_reference') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        <!-- Submit  -->
        <div class="text-center mt-6">
            <button type="submit"
                class="w-full bg-lime-600 text-white py-2 rounded-md hover:bg-lime-700 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
