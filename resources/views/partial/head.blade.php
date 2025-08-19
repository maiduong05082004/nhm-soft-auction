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

@include('partial.schema')

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    function showToast(message, type = 'success') {
        const isDesktop = window.innerWidth >= 640;
        const toastSelector = isDesktop ? '#toast' : '#mobile-toast';
        const contentSelector = isDesktop ? '#toast-content' : '#mobile-toast-content';
        const messageSelector = isDesktop ? '#toast-message' : '#mobile-toast-message';
        const iconSelector = isDesktop ? '#toast-icon' : '#mobile-toast-icon';

        const toastEl = document.querySelector(toastSelector);
        const contentEl = document.querySelector(contentSelector);
        const messageEl = document.querySelector(messageSelector);

        if (!toastEl || !contentEl || !messageEl) return;

        messageEl.textContent = message;
        console.log('djt me may js aaaaaaaaaaaaaaa');

        const colors = {
            success: 'border-green-500',
            error: 'border-red-500',
            info: 'border-blue-500'
        };

        contentEl.classList.remove('border-green-500', 'border-red-500', 'border-blue-500');
        contentEl.classList.add(colors[type] || colors.success);

        toastEl.classList.remove('hidden');

        setTimeout(() => {
            contentEl.classList.remove(isDesktop ? 'translate-x-full' : 'translate-y-full');
        }, 10);

        setTimeout(() => {
            contentEl.classList.add(isDesktop ? 'translate-x-full' : 'translate-y-full');
            setTimeout(() => {
                toastEl.classList.add('hidden');
            }, 300);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
            showToast({!! json_encode(session('success'), JSON_UNESCAPED_UNICODE) !!}, 'success');
        @endif
        @if (session('error'))
            showToast({!! json_encode(session('error'), JSON_UNESCAPED_UNICODE) !!}, 'error');
        @endif
    });
</script>
