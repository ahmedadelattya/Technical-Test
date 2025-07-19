<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    @include('dashboard.partials.sidebar')

    <div class="ml-64 p-6">
        @include('dashboard.partials.navbar')

        <main>
            @yield('content')
        </main>
    </div>

</body>

</html>
