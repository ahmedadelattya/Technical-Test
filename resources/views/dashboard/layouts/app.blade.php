<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
            class="fixed bottom-6 right-6 z-50 bg-white border border-gray-200 shadow-xl rounded-lg p-4 w-full max-w-sm">
            <div class="font-semibold text-sm text-gray-800">
                {{ session('success_title', 'Success') }}
            </div>
            <div class="text-gray-600 text-sm mt-1">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @include('dashboard.partials.sidebar')

    <div class="ml-64 p-6">
        @include('dashboard.partials.navbar')

        <main>
            @yield('content')
        </main>
    </div>

</body>

</html>
