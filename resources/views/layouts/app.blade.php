<!DOCTYPE html>
<html lang="en" id="html-root">

    <head>
        <meta charset="UTF-8">
        <title>Grocery Club</title>
        @vite('resources/css/app.css')
        @livewireStyles
        <style>
            summary {
                list-style: none;
                -webkit-appearance: none;
                appearance: none;
            }
        </style>
        <script>
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>
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

            <div class="hidden md:flex items-center gap-6">
                @can('manage', App\Models\User::class)
                <div class="flex justify-center">
                    <details class="relative text-center">
                        <summary class="cursor-pointer text-green-700 dark:text-green-300 font-semibold py-2">
                            Business Settings
                        </summary>
                        <div class="absolute mt-2 left-1/2 transform -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow z-50 w-48 px-2 py-2 flex flex-col text-center">
                            <a href="{{ route('categories.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Categories</a>
                            <a href="{{ route('products.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Products</a>
                            <a href="{{ route('settings.edit') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Membership Fee</a>
                            <a href="{{ route('shipping-costs.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Shipping Costs</a>
                        </div>
                    </details>
                </div>
                @endcan
                <button type="button" onclick="toggleTheme()" class="text-3xl hover:text-yellow-500 transition" title="Toggle dark mode">
                    <span id="theme-icon">🌙</span>
                </button>

                @php $type = Auth::check() ? Auth::user()->type : null; @endphp
                @if($type !== 'employee')
                    <a href="{{ route('recommended') }}" class="text-3xl hover:text-yellow-500 transition">
                        👑
                    </a>

                    <a href="{{ route('wishlist') }}" class="relative text-pink-600 hover:text-pink-800 transition" title="Wishlist" style="font-size: 1.8rem;">
                        🧡
                        @php $wishlist = session('wishlist', []); $wishlistCount = count($wishlist); @endphp
                        @if($wishlistCount > 0)
                            <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">{{ $wishlistCount }}</span>
                        @endif
                    </a>
                @endif

                <a href="{{ route('cart.view') }}" class="relative text-green-700 dark:text-green-300 hover:text-green-900 transition text-2xl">
                    🛒
                    @php $cart = session('cart', []); $totalItems = array_sum(array_column($cart, 'quantity')); @endphp
                    @if($totalItems > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1 rounded-full">{{ $totalItems }}</span>
                    @endif
                </a>

                @auth
                    <details class="relative">
                        <summary class="flex items-center gap-2 cursor-pointer text-green-700 dark:text-green-300">
                            <img src="{{ Auth::user()->profile_image_url }}" alt="Profile" class="w-12 h-12 rounded-full object-cover border">
                            <span>{{ Auth::user()->name }}</span>
                        </summary>
                        <div class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow z-50 min-w-[250px] px-4 py-2 flex flex-col">
                            <a href="{{ route('profile.show', Auth::user()) }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">👤 Profile</a>
                            <a href="{{ route('changePassword') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🔑 Change password</a>
                            @php $type = Auth::user()->type; @endphp
                            @if($type === 'member' || $type === 'board')
                                <a href="{{ route('card.show') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">💳 Card</a>
                            @endif

                            @if(in_array(Auth::user()->type, ['employee', 'board']))
                                <a href="{{ route('order.pending') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📦 Pending Orders</a>
                                <a href="{{ route('inventory.index') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📊 Inventory</a>
                            @endif

                            @if($type === 'board')
                                <a href="{{ route('board.users.index') }}" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📂 User Management</a>
                            @endif

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
                        <a href="{{ route('profile.show', Auth::user()) }}" class="block px-9 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">👤 Profile</a>
                        <button type="button" onclick="toggleTheme()" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 w-full text-left">
                            <span id="theme-icon-mobile">🌙</span>
                            <span id="theme-label-mobile" class="ml-2">Dark Mode</span>
                        </button>
                        @if($type !== 'employee')
                            <a href="{{ route('recommended') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">👑 Recommended </a>
                            <a href="{{ route('wishlist') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🧡 Wishlist</a>
                        @endif
                        <a href="{{ route('cart.view') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🛒 Cart</a>
                        @php $type = Auth::user()->type; @endphp
                        @if($type === 'member' || $type === 'board')
                            <a href="{{ route('card.show') }}" class="px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-green-700 dark:text-green-300">💳 Card</a>
                        @endif
                        <a href="{{ route('changePassword') }}" class="px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-green-700 dark:text-green-300">🔑 Change password</a>
                        
                        @if(in_array(Auth::user()->type, ['employee', 'board']))
                            <a href="{{ route('order.pending') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📦 Pending Orders</a>
                            <a href="{{ route('inventory.index') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📊 Inventory</a>
                        @endif

                        @if($type === 'board')
                            <a href="{{ route('board.users.index') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">📂 User Management</a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-red-600">🚪 Logout</button>
                        </form>
                        @can('manage', App\Models\User::class)
                        <div class="flex justify-left">
                            <details class="relative text-left">
                                <summary class="px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm text-green-700 dark:text-green-300">
                                    💼 Business Settings
                                </summary>
                                <div class="absolute mt-2 left-1/2 transform -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded shadow z-50 w-48 px-2 py-2 flex flex-col text-center">
                                    <a href="{{ route('categories.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Categories</a>
                                    <a href="{{ route('products.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Products</a>
                                    <a href="{{ route('settings.edit') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Membership Fee</a>
                                    <a href="{{ route('shipping-costs.index') }}" class="px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 text-sm">Shipping Costs</a>
                                </div>
                            </details>
                        </div>
                        @endcan
                    @else
                        <button type="button" onclick="toggleTheme()" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300 w-full text-left">
                            <span id="theme-icon-mobile">🌙</span>
                            <span id="theme-label-mobile" class="ml">Dark Mode</span>
                        </button>
                        @if($type !== 'employee')
                            <a href="{{ route('recommended') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">👑 Recommended </a>
                            <a href="{{ route('wishlist') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🧡 Wishlist</a>
                        @endif
                        <a href="{{ route('cart.view') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🛒 Cart</a>
                        <a href="{{ route('login') }}" class="block px-8 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-700 dark:text-green-300">🔐 Login</a>
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

        <script>
            function toggleTheme() {
                const html = document.documentElement;
                const isDark = html.classList.contains('dark');

                const icon = document.getElementById('theme-icon');
                const mobileIcon = document.getElementById('theme-icon-mobile');
                const mobileLabel = document.getElementById('theme-label-mobile');

                if (isDark) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    icon.textContent = '🌙';
                    if (mobileIcon) mobileIcon.textContent = '🌙';
                    if (mobileLabel) mobileLabel.textContent = 'Dark Mode';
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    icon.textContent = '☀️';
                    if (mobileIcon) mobileIcon.textContent = '☀️';
                    if (mobileLabel) mobileLabel.textContent = 'Light Mode';
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const saved = localStorage.getItem('theme');
                const icon = saved === 'dark' ? '☀️' : '🌙';
                const label = saved === 'dark' ? 'Light Mode' : 'Dark Mode';

                document.getElementById('theme-icon').textContent = icon;
                const mobileIcon = document.getElementById('theme-icon-mobile');
                const mobileLabel = document.getElementById('theme-label-mobile');
                if (mobileIcon) mobileIcon.textContent = icon;
                if (mobileLabel) mobileLabel.textContent = label;
            });
        </script>

    </body>
</html>
