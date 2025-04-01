<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name') . ' - Blog')</title>
    
    <!-- Meta tags -->
    @yield('meta')
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</a>


                <div class="flex items-center space-x-4">
                    <nav>
                        <ul class="flex space-x-6">
                            <li><a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800">Home</a></li>
                            <li><a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-800">About Us</a></li>
                            <li><a href="{{ route('contact') }}" class="text-gray-600 hover:text-gray-800">Contact</a></li>
    
                            @auth
                                <li><a href="{{ route('dashboard') }}" class="p-2 rounded-md border border-gray-200 text-gray-600 hover:text-gray-800">Dashboard</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a></li>
                            @endauth
                        </ul>
                    </nav>
    
                    <livewire:search />
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="container mx-auto py-6 px-4">
            <div class="text-center text-sm text-gray-500">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>