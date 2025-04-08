<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') . ' - Blog' }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">


    <!-- Meta tags -->
    @yield('meta')
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    @include('layouts.navigation')

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