<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Auctions')</title>
    @include('partial.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F2F1EB] text-gray-800 font-roboto">
    @include('partial.header')
    <main class="max-w-7xl mx-auto py-6">
        @yield('content')
    </main>
    @include('partial.footer')
</body>

</html>
