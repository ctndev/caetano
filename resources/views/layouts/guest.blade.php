<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Caetano') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ config('app.name', 'Caetano') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Sistema de Vendas via WhatsApp</p>
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
