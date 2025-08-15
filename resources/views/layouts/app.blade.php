<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Auctions')</title>
    @include('partial.head')
    @vite(['resources/css/app.css'])
</head>

<body class="bg-[#F2F1EB] text-gray-800 font-roboto">
    <div class="overflow-hidden bg-yellow-300 py-2">
        <div class="whitespace-nowrap animate-marquee text-sm font-medium text-gray-800">
            🎉 Khuyến mãi đặc biệt! Miễn phí vận chuyển cho đơn hàng từ 500k 🎉 &nbsp;&nbsp;|&nbsp;&nbsp;
            🚚 Giao hàng toàn quốc nhanh chóng 🚚 &nbsp;&nbsp;|&nbsp;&nbsp;
            💳 Thanh toán linh hoạt, an toàn 💳
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
    @vite(['resources/js/app.js', 'resources/js/partials/menu-mobile.js', 'resources/js/partials/slide.js'])
</body>

</html>
