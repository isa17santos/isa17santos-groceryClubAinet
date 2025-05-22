<!DOCTYPE html>
<html lang="en" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <title>Grocery Club</title>
    @vite('resources/css/app.css') <!-- tailwind -->
    @livewireStyles
    <style>
        summary {
            list-style: none;
            -webkit-appearance: none;
            appearance: none;
        }
    </style>
</head>

@livewireScripts
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">

    <nav class="fixed top-0 w-full z-50 bg-white dark:bg-gray-800 shadow-md h-[72px] px-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('catalog') }}" class="hover:opacity-80 transition">
                <img src="{{ asset('/images/logo.png') }}" alt="Grocery Club" class="h-24">
            </a>
            <span class="text-3xl font-bold text-yellow-700 dark:text-yellow-700">Grocery Club</span>
        </div>

        <!-- Desktop Navbar -->
        <div class="hidden md:flex items-center gap-6">
            <form action="{{ route('toggle.theme') }}" method="POST">
                @csrf
                <button type="submit" class="text-xl hover:text-yellow-500 transition" title="Toggle dark mode">
                    {{ session('theme', 'light') === 'dark' ? '☀️' : '🌙' }}
                </button>
            </form>

            <!-- Wishlist Link -->
            <a href="{{ route('wishlist') }}" class="relative text-pink-600 hover:text-pink-800 transition" title="Wishlist" style="font-size: 1.8rem;">
                🧡
                @php
                    $wishlist = session('wishlist', []);
                    $wishlistCount = count($wishlist);
                @endphp
                @if($wishlistCount > 0)
                    <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">
                        {{ $wishlistCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('cart.view') }}" class="relative text-green-700 dark:text-green-300 hover:text-green-900 transition text-2xl">
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

            @auth
                <details class="relative">
                    <summary class="flex items-center gap-2 cursor-pointer text-green-700 dark:text-green-300">
                        <img src="{{ Auth::user()->profile_image_url }}" alt="Profile" class="w-12 h-12 rounded-full object-cover border">
                        <span>{{ Auth::user()->name }}</span>
                    </summary>

                    <div class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow z-50 min-w-[250px] px-4 py-2 flex flex-col">
                        <a href="{{ route('changePassword') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🔑 Change password</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-red-500">🚪 Logout</button>
                        </form>
                    </div>
                </details>
            @else
                <a href="{{ route('login') }}" class="text-green-700 dark:text-green-300 hover:underline text-2xl">Login</a>
            @endauth

        </div>

        <!-- Mobile Burger Menu -->
        <details class="md:hidden relative">
            <summary class="text-3xl cursor-pointer select-none">☰</summary>
            <div class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow-md py-3 w-64 text-left flex flex-col z-50 text-base">
                @auth
                    <div class="flex items-center gap-2 px-8 py-2 text-green-700 dark:text-green-300">
                        <img src="{{ Auth::user()->profile_image_url }}" alt="Profile" class="w-9 h-9 rounded-full object-cover border">
                        <span>{{ Auth::user()->name }}</span>
                    </div>

                    <form action="{{ route('toggle.theme') }}" method="POST" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        @csrf
                        <button type="submit" class="w-full text-left">
                            {{ session('theme', 'light') === 'dark' ? '☀️ Light mode' : '🌙 Dark mode' }}
                        </button>
                    </form>

                    <a href="{{ route('wishlist') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        🧡 Wishlist
                    </a>


                    <a href="{{ route('cart.view') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        🛒 Cart
                    </a>

                    <a href="{{ route('changePassword') }}" class="px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-green-700 dark:text-green-300">
                        🔑 Change password
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-red-600">🚪 Logout</button>
                    </form>
                @else
                    <form action="{{ route('toggle.theme') }}" method="POST" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        @csrf
                        <button type="submit" class="w-full text-left">
                            {{ session('theme', 'light') === 'dark' ? '☀️ Light mode' : '🌙 Dark mode' }}
                        </button>
                    </form>

                    <a href="{{ route('wishlist') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        🧡 Wishlist
                    </a>


                    <a href="{{ route('cart.view') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        🛒 Cart
                    </a>
                    <a href="{{ route('login') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">
                        🔐 Login
                    </a>
                @endauth
            </div>
        </details>

    </nav>

    <main class="min-h-screen mt-[120px]">
        @yield('content')
    </main>

    <footer class="bg-white dark:bg-gray-800 text-center py-4 text-sm text-gray-500 dark:text-gray-400 mt-8">
        &copy; {{ date('Y') }} Grocery Club. All rights reserved.
    </footer>
</body>

</html>
