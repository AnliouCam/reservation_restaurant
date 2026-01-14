<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Restaurant') }}</title>

        <!-- Fonts Google -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-anthracite">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
                <!-- Top Bar (mobile menu + user) -->
                @include('layouts.topbar')

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="px-4 sm:px-6 lg:px-8 py-4">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <!-- Messages Flash -->
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-status-disponible text-green-800 px-4 py-3 rounded-r" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border-l-4 border-status-occupee text-red-800 px-4 py-3 rounded-r" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 py-4 px-6 text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Restaurant') }}
                </footer>
            </div>
        </div>
    </body>
</html>
