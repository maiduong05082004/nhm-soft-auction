{{-- Improved Header Component --}}
@push('styles')
    <style>
        /* Header Animations */
        .header-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Menu Animations */
        .slide-in-right {
            animation: slideInRight 0.3s ease-out forwards;
        }

        .slide-out-right {
            animation: slideOutRight 0.3s ease-out forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(100%);
            }
        }

        /* Search Popup Animations */
        .popup-fade-in {
            animation: popupFadeIn 0.3s ease-out forwards;
        }

        .popup-fade-out {
            animation: popupFadeOut 0.3s ease-out forwards;
        }

        @keyframes popupFadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @keyframes popupFadeOut {
            from {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            to {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }
        }

        /* Logo hover effect */
        .logo-text {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text:hover {
            transform: scale(1.02);
        }

        /* Navigation icons */
        .nav-icon {
            transition: all 0.3s ease;
        }

        .nav-icon:hover {
            transform: translateY(-2px);
            color: #667eea;
        }

        /* Mobile menu backdrop blur */
        .menu-backdrop {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>
@endpush

<header class="bg-white/95 backdrop-blur-sm sticky top-0 z-40  header-fade-in">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center justify-between h-16 lg:h-20" aria-label="Main navigation">

            <div class="flex-1 flex items-center lg:justify-start justify-center">
                <a href=""
                    class="logo-text  uppercase text-2xl sm:text-3xl lg:text-4xl xl:text-5xl 
                          tracking-wider sm:tracking-widest lg:tracking-[0.2em] xl:tracking-[0.3em]
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    aria-label="Takada Ooku - Trang chủ">
                    Takada Ooku
                </a>
            </div>

            <div class="hidden lg:flex items-center space-x-8">

                <div class="flex items-center space-x-6">
                    <div class="relative group">
                        <button
                            class="nav-icon flex flex-col items-center p-2 text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2  transition-all"
                            aria-expanded="false" aria-haspopup="true">
                            <x-heroicon-o-user class="w-6 h-6 mb-1" />
                                <span class="text-xs font-medium">Tài khoản</span>
                        </button>
                    </div>
                    <button id="openPopup"
                        class="nav-icon flex flex-col items-center p-2 text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2  transition-all"
                        aria-label="Mở tìm kiếm">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                        <span class="text-xs font-medium">Tìm kiếm</span>
                    </button>

                    <a href=""
                        class="nav-icon flex flex-col items-center p-2 text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2  transition-all relative">
                        <x-heroicon-o-heart class="w-6 h-6 mb-1" />
                        <span class="text-xs font-medium">Yêu thích</span>
                    </a>
                </div>

            </div>

            <button id="mobileMenuToggle" data-open-mobile-menu
                class="lg:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2  transition-colors"
                aria-label="Mở menu di động" aria-expanded="false">
                <x-heroicon-o-bars-3 class="h-6 w-6 mb-1" />
            </button>
        </nav>
    </div>
</header>

<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 menu-backdrop hidden " aria-hidden="true">
</div>


<aside id="mobileMenuPanel" class="fixed top-0 right-0 h-full w-80 max-w-[90vw] bg-white shadow-2xl z-50 hidden"
    role="dialog" aria-modal="true" aria-labelledby="mobile-menu-title">

    <div
        class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50">
        <h2 id="mobile-menu-title" class="text-lg font-semibold text-gray-900">Menu</h2>
        <button id="closeMobileMenu"
            class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500  transition-colors"
            aria-label="Đóng menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="flex flex-col h-full overflow-y-auto">


        <div class="p-4 bg-gray-50">
            <form action="" method="GET" class="space-y-3">
                <div class="relative">
                    <input type="search" name="q" placeholder="Tìm kiếm sản phẩm..."
                        class="w-full pl-10 pr-4 py-2  focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        aria-label="Tìm kiếm sản phẩm">
                    <x-heroicon-o-magnifying-glass class="w-6 h-6 mb-1" />
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2  font-medium hover:bg-blue-700 transition-colors">
                    Tìm kiếm
                </button>
            </form>
        </div>
        <nav class="flex-1 p-4" aria-label="Mobile navigation">
        </nav>
    </div>
</aside>


<div id="popupOverlay" class="fixed inset-0 bg-black/50 menu-backdrop hidden items-center justify-center z-50"
    aria-hidden="true">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform scale-95 transition-all duration-300"
        role="dialog" aria-modal="true" aria-labelledby="search-title">

        <div class="flex items-center justify-between p-6 ">
            <h3 id="search-title" class="text-lg font-semibold text-gray-900">Tìm kiếm sản phẩm</h3>
            <button id="closeBtn"
                class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500  transition-colors"
                aria-label="Đóng tìm kiếm">
                <x-heroicon-o-magnifying-glass class="w-6 h-6 mb-1" />
            </button>
        </div>


        <div class="p-6">
            <form action="" method="GET" class="space-y-4">
                <div class="relative">
                    <input type="search" name="q" id="searchInput" placeholder="Nhập từ khóa tìm kiếm..."
                        class="w-full pl-12 pr-4 py-3 text-lg  focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        aria-label="Từ khóa tìm kiếm" autocomplete="off">
                    <svg class="absolute left-4 top-3.5 w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-700">Gợi ý tìm kiếm:</p>
                    <div class="flex flex-wrap gap-2">

                    </div>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3  font-medium hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Tìm kiếm
                    </button>
                    <button type="button" id="closePopup"
                        class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
