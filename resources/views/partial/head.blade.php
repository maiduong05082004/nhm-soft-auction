<title>@yield('title')</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="@yield('meta_description', 'Mô tả mặc định cho trang đấu giá của bạn')">
<meta name="keywords" content="@yield('meta_keywords', 'đấu giá, mua bán, auctions')">
<meta name="author" content="Tên của bạn">

<!-- Open Graph -->
<meta property="og:title" content="@yield('og_title', 'Auctions Clone')">
<meta property="og:description" content="@yield('og_description', 'Mô tả mặc định cho Open Graph')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="@yield('og_image', asset('images/default-og.jpg'))">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('twitter_title', 'Auctions Clone')">
<meta name="twitter:description" content="@yield('twitter_description', 'Mô tả cho Twitter Card')">
<meta name="twitter:image" content="@yield('twitter_image', asset('images/default-twitter.jpg'))">

<!-- Canonical -->
<link rel="canonical" href="{{ url()->current() }}">

<!-- JSON-LD Schema.org -->
@include('partial.schema')

@vite(['resources/css/app.css', 'resources/js/app.js'])
