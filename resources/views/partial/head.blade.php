<title>@yield('title', 'Takara-ooku')</title>
<meta name="description" content="@yield('meta_description', 'Mô tả mặc định cho trang trả giá trực tuyến của bạn')">
<meta name="keywords" content="@yield('meta_keywords', 'Trả giá, mua bán, auctions')">
<meta name="author" content="Tên của bạn">

<!-- Open Graph -->
<meta property="og:title" content="@yield('og_title', 'Takara-ooku')">
<meta property="og:description" content="@yield('og_description', 'Mô tả mặc định cho Open Graph')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="@yield('og_image', asset('images/default-og.jpg'))">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('twitter_title', 'Takara-ooku')">
<meta name="twitter:description" content="@yield('twitter_description', 'Mô tả cho Twitter Card')">
<meta name="twitter:image" content="@yield('twitter_image', asset('images/default-twitter.jpg'))">
<!-- Buộc thông báo chỉ hỗ trợ light mode -->
<meta name="color-scheme" content="only light">
<meta name="supported-color-schemes" content="light">

<!-- Theme color (address bar / mobile) - khai báo rõ cho light -->
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">

<style>
  /* Tối đa hoá loại trừ "auto dark" của browser */
  :root, html, body {
    color-scheme: only light !important;
    background-color: ##f2f4f6 !important;
    color: #111111 !important;
    /* Tắt forced colors trên những browser hỗ trợ */
    forced-color-adjust: none;
  }
</style>

<!-- Canonical -->
<link rel="canonical" href="{{ url()->current() }}">

@include('partial.schema')


@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/partials/wishlist.js'])
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
            @if (str_contains(session('success'), 'Trả giá thành công'))
                @if (session('user_bid_data'))
                    showAuctionSuccessToast({!! json_encode(session('user_bid_data'), JSON_UNESCAPED_UNICODE) !!});
                @else
                    showAuctionSuccessToast();
                @endif
            @else
                showToast({!! json_encode(session('success'), JSON_UNESCAPED_UNICODE) !!}, 'success');
            @endif
        @endif
        @if (session('error'))
            showToast({!! json_encode(session('error'), JSON_UNESCAPED_UNICODE) !!}, 'error');
        @endif
    });
</script>
