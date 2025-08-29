<!doctype html>
<html lang="vi" theme="light" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.ico') }}">

    @include('partial.head')
    @vite(['resources/css/app.css'])
</head>

<body class="bg-[#f2f4f6] text-gray-800 font-roboto">
    <div class="overflow-hidden bg-yellow-300 py-2">
        <div class="whitespace-nowrap animate-marquee text-sm font-medium text-gray-800">
            {{ $marquee ??
                '🎉 Khuyến mãi đặc biệt! Miễn phí vận chuyển cho đơn hàng từ 500k 🎉  |
                                        🚚 Giao hàng toàn quốc nhanh chóng 🚚  |
                                        💳 Thanh toán linh hoạt, an toàn 💳' }}
        </div>

    </div>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            animation: marquee 12s linear infinite;
        }
    </style>

    @include('partial.header')
    <main class="">
        @yield('content')
    </main>
    @include('partial.footer')
    @vite(['resources/js/app.js', 'resources/js/partials/slide.js', 'resources/js/partials/header.js'])
</body>

</html>
