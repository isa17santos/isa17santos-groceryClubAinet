<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Grocery Club</title>
    @vite('resources/css/app.css') <!-- tailwind -->
</head>

<body class="bg-gray-100 text-gray-900">

    <nav class="fixed top-0 w-full z-50 bg-white shadow-md h-[64px] overflow-hidden px-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Grocery Club" class="h-24">
            <span class="text-xl font-bold text-yellow-700">Grocery Club</span>
        </div>
        <div class="flex items-center gap-6">
            <!-- Dark Mode Toggle Placeholder -->
            <button id="toggle-theme"
                class="text-gray-600 hover:text-black transition"
                title="Toggle dark mode"
                style="font-size: 1.8rem;">
                🌙
            </button>

            <!-- Cart Link -->
            <a href="{{ route('cart.view') }}" class="relative text-green-700 hover:text-green-900 transition" style="font-size: 1.8rem;">
                🛒
                @php
                $cart = session('cart', []);
                $totalItems = array_sum(array_column($cart, 'quantity'));
                @endphp
                @if($totalItems > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">
                    {{ $totalItems }}
                </span>
                @endif
            </a>

            <!-- Login Link (simple) -->
            <a href="{{ route('login') }}" class="text-green-700 hover:underline text-2xl">Login</a>
        </div>
    </nav>

    <main class="min-h-screen mt-[120px]">
        @yield('content')
    </main>

    <footer class="bg-white text-center py-4 text-sm text-gray-500 mt-8">
        &copy; {{ date('Y') }} Grocery Club. All rights reserved.
    </footer>
</body>

</html>