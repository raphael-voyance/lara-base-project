<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        @livewireScripts
        {!! ToastMagic::styles() !!}
        @stack('styles')
    </head>
    <body class="font-sans antialiased min-h-screen bg-gray-50">
        <div class="flex flex-col justify-center min-h-screen py-12 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>

        @include('cookie-consent::index')
        {!! ToastMagic::scripts() !!}
        @stack('scripts')
    </body>
</html>
