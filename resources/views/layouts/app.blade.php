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
        <div class="space-x-6">
            <a href="/" class="text-green-700 hover:underline">Início</a>
            <a href="#" class="text-green-700 hover:underline">Sobre</a>
            <a href="#" class="text-green-700 hover:underline">Contacto</a>
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
