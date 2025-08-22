<div x-data="{
    mobileMenuOpen: false,
    searchOpen: false,
    searchQuery: '',

    openMobileMenu() {
        this.mobileMenuOpen = true;
        document.body.classList.add('overflow-hidden');
    },

    closeMobileMenu() {
        this.mobileMenuOpen = false;
        document.body.classList.remove('overflow-hidden');
    },

    openSearch() {
        this.searchOpen = true;
        document.body.classList.add('overflow-hidden');
        this.$nextTick(() => this.$refs.searchInput.focus());
    },

    closeSearch() {
        this.searchOpen = false;
        document.body.classList.remove('overflow-hidden');
        this.searchQuery = '';
    },

    selectSuggestion(suggestion) {
        this.searchQuery = suggestion;
        this.$refs.searchInput.focus();
    }
}"
    @keydown.escape="mobileMenuOpen = false; searchOpen = false; document.body.classList.remove('overflow-hidden')">
    <div id="config-route" data-wishlist-get="{{ route('wishlist.get-items') }}"
        data-wishlist-add="{{ route('wishlist.add') }}" data-wishlist-remove="{{ route('wishlist.remove') }}"
        data-wishlist-clear="{{ route('wishlist.clear') }}" data-cart-add="{{ route('cart.add', [':id']) }}"
        data-home="{{ route('home') }}" data-product-detail="{{ route('products.show', [':id']) }}">
    </div>


    @php
        $headerCartCount = $headerCartCount ?? 0;
        $headerWishlistCount = $headerWishlistCount ?? 0;
    @endphp

    <div id="toast" class="fixed top-4 right-0 z-50 hidden sm:block">
        <div class="bg-white border-l-4 text-gray-700 p-3 sm:p-4 rounded shadow-lg max-w-xs sm:max-w-sm transition-all duration-300 transform translate-x-full"
            id="toast-content">
            <div class="flex items-center">
                <div class="flex-shrink-0" id="toast-icon">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-2 sm:ml-3">
                    <p id="toast-message" class="text-xs sm:text-sm font-medium"></p>
                </div>
            </div>
        </div>
    </div>

    

    <div id="mobile-toast" class="fixed bottom-4 left-4 right-4 z-50 hidden sm:hidden">
        <div class="bg-white border-l-4 text-gray-700 p-3 rounded shadow-lg transition-all duration-300 transform translate-y-full"
            id="mobile-toast-content">
            <div class="flex items-center">
                <div class="flex-shrink-0" id="mobile-toast-icon">
                    <svg class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-2">
                    <p id="mobile-toast-message" class="text-xs font-medium"></p>
                </div>
            </div>
        </div>
    </div>

    <header class="bg-white/95 backdrop-blur-sm sticky top-0 z-40 animate-(--animate-header-fade-in) shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16 lg:h-20" aria-label="Main navigation">

                <div class="flex-1 flex items-center lg:justify-start justify-center">
                    <a href="{{ route('home') }}"
                        class="logo-gradient uppercase text-2xl sm:text-3xl lg:text-4xl xl:text-5xl
                          tracking-wider sm:tracking-widest lg:tracking-[0.2em] xl:tracking-[0.3em]
                          font-bold transition-transform duration-300 hover:scale-[1.02]"
                        aria-label="Takada Ooku - Trang chủ">
                        Takada Ooku
                    </a>
                </div>

                <div class="hidden lg:flex items-center space-x-8">
                    <div class="flex items-center space-x-6">

                        <a href="{{ route('products.list') }}"
                            class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                               transition-all duration-300 hover:-translate-y-0.5 relative">
                            <x-heroicon-o-shopping-bag class="w-6 h-6 mb-1"></x-heroicon-o-shopping-bag>
                            <span class="text-xs font-medium">sản phẩm</span>
                        </a>

                        <a href="{{ route('news.list') }}"
                            class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                               transition-all duration-300 hover:-translate-y-0.5 relative">
                            <x-heroicon-o-newspaper class="w-6 h-6 mb-1"></x-heroicon-o-newspaper>
                            <span class="text-xs font-medium">Tin tức</span>
                        </a>

                        <div class="relative group">
                            <a href="{{ route('home') . '/admin' }}"
                                class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                                   transition-all duration-300 cursor-pointer hover:-translate-y-0.5"
                                aria-expanded="false" aria-haspopup="true">
                                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium">Tài khoản</span>
                            </a>
                        </div>

                        <button @click="openSearch()"
                            class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                               transition-all duration-300 hover:-translate-y-0.5"
                            aria-label="Mở tìm kiếm">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="text-xs font-medium">Tìm kiếm</span>
                        </button>

                        <a href="{{ route('wishlist.list') }}"
                            class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                            transition-all duration-300 hover:-translate-y-0.5 relative">
                            <x-heroicon-o-heart class="w-6 h-6 mb-1"></x-heroicon-o-heart>
                            @auth
                                <span id="wishlist-count" 
                                    class="absolute -top-0.5 right-3 !bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow">
                                    {{ $headerWishlistCount > 0 ? $headerWishlistCount : '' }}
                                </span>
                            @endauth
                            <span class="text-xs font-medium">Yêu thích</span>
                        </a>
                        <a href="{{ route('cart.index') }}"
                            class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600
                               transition-all duration-300 hover:-translate-y-0.5 relative">
                            <x-heroicon-o-shopping-cart class="w-6 h-6 mb-1"></x-heroicon-o-shopping-cart>
                            @auth
                                @if ($headerCartCount > 0)
                                    <span id="header-cart-count" class="absolute -top-0.5 right-3 !bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow">
                                        {{ $headerCartCount }}
                                    </span>
                                @endif
                            @endauth
                            <span class="text-xs font-medium">Giỏ hàng</span>
                        </a>
                    </div>
                </div>

                <button @click="openMobileMenu()"
                    class="lg:hidden p-2 text-gray-600 hover:text-gray-900
                       transition-colors duration-300"
                    aria-label="Mở menu di động" :aria-expanded="mobileMenuOpen">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="closeMobileMenu()"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40" style="display: none;" aria-hidden="true">
    </div>

    <aside x-show="mobileMenuOpen" x-transition:enter="transition-transform duration-300"
        x-transition:enter-start="transform translate-x-full" x-transition:enter-end="transform translate-x-0"
        x-transition:leave="transition-transform duration-300" x-transition:leave-start="transform translate-x-0"
        x-transition:leave-end="transform translate-x-full"
        class="fixed top-0 right-0 h-full w-80 max-w-[90vw] bg-white shadow-2xl z-50" style="display: none;"
        role="dialog" aria-modal="true" aria-labelledby="mobile-menu-title">

        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50">
            <h2 id="mobile-menu-title" class="text-lg font-semibold text-gray-900">Menu</h2>
            <button @click="closeMobileMenu()"
                class="p-2 text-gray-500 hover:text-gray-700
                   transition-colors duration-300
                   focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                aria-label="Đóng menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="flex flex-col h-full overflow-y-auto">
            <div class="p-4 bg-gray-50">
                <form action="{{ route('products.list') }}" method="GET" class="space-y-3">
                    <div class="relative">
                        <input type="search" name="name" placeholder="Tìm kiếm sản phẩm..."
                            x-model="searchQuery" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg"
                            aria-label="Tìm kiếm sản phẩm">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium
                           hover:bg-blue-700 transition-colors duration-300">
                        Tìm kiếm
                    </button>
                </form>
            </div>
            <nav class="flex-1 p-4" aria-label="Mobile navigation">
                <div class="grid grid-cols-3 md:grid-cols-5 lg:hidden gap-3 mb-4">
                    @if (isset($categories))
                        @foreach ($categories as $category)
                            <div class="text-center">
                                <a href="{{ route('products.list', ['category_id' => $category->id]) }}"
                                    class="block hover:opacity-80 transition-opacity">
                                    @if (isset($category['image']))
                                        <img src="{{ \App\Utils\HelperFunc::generateURLFilePath($category['image']) }}"
                                            class="w-16 h-16 mx-auto mb-2 rounded-lg object-cover"
                                            alt="{{ $category['name'] }}" loading="lazy" />
                                    @else
                                        <img src="{{ asset('images/product_default.jpg') }}"
                                            class="w-16 h-16 mx-auto mb-2 rounded-lg object-cover"
                                            alt="{{ $category['name'] }}" loading="lazy" />
                                    @endif
                                    <h3 class="text-xs font-medium text-gray-700 leading-tight">
                                        {{ $category['name'] }}
                                    </h3>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <h2>Không có danh mục nào</h2>
                    @endif
                </div>
            </nav>
        </div>
    </aside>


    <div x-show="searchOpen" x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="if ($event.target === $el) closeSearch()"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
        style="display: none;" aria-hidden="true">

        <div x-show="searchOpen" x-transition:enter="transition-all duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition-all duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" @click.stop
            class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4" role="dialog" aria-modal="true"
            aria-labelledby="search-title">

            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 id="search-title" class="text-lg font-semibold text-gray-900">Tìm kiếm sản phẩm</h3>
                <button @click="closeSearch()" class="p-2 text-gray-400 hover:text-gray-600"
                    aria-label="Đóng tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('products.list') }}" method="GET" class="space-y-4">
                    <div class="relative">
                        <input type="search" name="name" x-ref="searchInput" x-model="searchQuery"
                            placeholder="Nhập từ khóa tìm kiếm..."
                            class="w-full pl-12 pr-4 py-3 text-lg border border-gray-300"
                            aria-label="Từ khóa tìm kiếm" autocomplete="off">
                        <svg class="absolute left-4 top-3.5 w-6 h-6 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    {{--
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-700">Gợi ý tìm kiếm:</p>
                        <div class="flex flex-wrap gap-2">
                            <span @click="selectSuggestion('Áo thun')"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 cursor-pointer transition-colors">
                                Áo thun
                            </span>
                            <span @click="selectSuggestion('Giày sneaker')"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 cursor-pointer transition-colors">
                                Giày sneaker
                            </span>
                            <span @click="selectSuggestion('Túi xách')"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 cursor-pointer transition-colors">
                                Túi xách
                            </span>
                        </div>
                    </div> --}}

                    <div class="flex space-x-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-medium
                               hover:bg-blue-700 transition-colors duration-300">
                            Tìm kiếm
                        </button>
                        <button type="button" @click="closeSearch()"
                            class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
