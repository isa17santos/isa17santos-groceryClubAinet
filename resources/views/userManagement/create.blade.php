@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)" 
        class="mb-4 p-3 bg-red-100 dark:bg-red-200 text-red-700 rounded-md shadow-sm max-w-2xl mx-auto"
    >
        <ul class="list-disc ml-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="max-w-2xl mx-auto mt-16 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
    <a href="{{ route('board.users.index') }}"
        class="inline-block mb-6 text-sm text-lime-700 dark:text-lime-400 hover:underline">
        ← Back to User Management
    </a>

    <h1 class="text-2xl font-bold text-yellow-700 dark:text-yellow-700 mb-6 text-center">
        Add New Employee
    </h1>

    <form method="POST" action="{{ route('board.users.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
            <select name="gender" required
                class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
                <option value="">Select gender</option>
                <option value="M" @selected(old('gender') === 'M')>Male</option>
                <option value="F" @selected(old('gender') === 'F')>Female</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
            <input type="text" value="Employee" disabled
                class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100 cursor-not-allowed">
        </div>


        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <input type="password" name="password" required
                class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo (optional)</label>
            
            @php
                $defaultPhoto = asset('storage/users/anonymous.png');
            @endphp
            <div x-data="{ photoPreview: '{{ $defaultPhoto }}' }" class="flex flex-col items-left">
                <img :src="photoPreview" alt="Photo Preview"
                    class="w-32 h-32 rounded-full object-cover border border-gray-300 dark:border-gray-600 mb-2">

                <input type="file" name="photo" accept="image/*"
                    @change="photoPreview = URL.createObjectURL($event.target.files[0])"
                    class="w-full mt-1 p-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="bg-lime-600 text-white py-2 px-6 rounded-md hover:bg-lime-700 transition text-md">
                Create Employee
            </button>
        </div>
    </form>
</div>
@endsection
