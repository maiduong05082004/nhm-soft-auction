import $ from "jquery";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/thumbs";
import "swiper/css/pagination";
import Swiper from "swiper";
import { Navigation, Thumbs, FreeMode, Pagination } from "swiper/modules";

window.$ = window.jQuery = $;
const APP_URL = 'http://127.0.0.1:8000';
document.addEventListener("DOMContentLoaded", () => {
    const thumbs = new Swiper(".thumbsSwiper", {
        modules: [FreeMode],
        spaceBetween: 8,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            0: { slidesPerView: 3 },
            480: { slidesPerView: 4 },
            640: { slidesPerView: 6 },
        },
    });

    const main = new Swiper(".mainSwiper", {
        modules: [Navigation, Thumbs],
        spaceBetween: 10,
        loop: document.querySelectorAll(".mainSwiper .swiper-slide").length > 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: { swiper: thumbs },
    });

    const popularProducts = new Swiper(".popularProductsSwiper", {
        modules: [Navigation, Pagination],
        spaceBetween: 16,
        slidesPerView: 1,
        navigation: {
            nextEl: ".popularProductsSwiper .swiper-button-next",
            prevEl: ".popularProductsSwiper .swiper-button-prev",
        },
        pagination: {
            el: ".popularProductsSwiper .swiper-pagination",
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            480: {
                slidesPerView: 2,
                spaceBetween: 16,
            },
            640: {
                slidesPerView: 3,
                spaceBetween: 16,
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
        },
    });
});


$(document).ready(function () {
    const renderCountWishlist = $('#wishlist-count');
    const config = $('#config-route');
    console.log(config);

    const API_ENDPOINTS = {
        wishlist_get: config.data('wishlist-get'),
        wishlist_add: config.data('wishlist-add'),
        wishlist_remove: config.data("wishlist-remove"),
        wishlist_clear: config.data('wishlist-clear'),
        cart_add: config.data('cart-add')
    };
    $(document).on('click', '.wishlist-btn', function (e) {
        e.preventDefault();
        const productId = $(this).attr('data-id');
        addWishlistItems(productId);
    });
    window.addWishlistItems = function (productId) {
        const productIdStr = productId.toString();
        $.ajax({
            url: API_ENDPOINTS.wishlist_add,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        }).then((res) => {
            changeCountWishlist(res);
        });
        changeCountWishlist();
        $.ajax({
            url: API_ENDPOINTS.wishlist_add,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                product_id: productIdStr
            },
            success: function (response) {

                if (response.success) {

                    showToast(response.message, 'success');
                }
            },
            error: function () {
                showToast('Lỗi khi thêm danh sách yêu thích: ', 'error');

            }
        });
    };


    loadWishlistItems();

    function changeCountWishlist(response) {

        const num = response.data.length;
        renderCountWishlist.append(num);
    }

    function loadWishlistItems() {

        $.ajax({
            url: API_ENDPOINTS.wishlist_get,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#loading-container').hide();
                if (response.success && response.data.length > 0) {
                    renderWishlistItems(response.data);
                    changeCountWishlist(response);
                    $('#content-container').show();
                    $('#action-buttons').show();
                    $('#mobile-action-buttons').show();
                } else {
                    $('#empty-container').show();
                }
            },
            error: function () {
                $('#loading-container').hide();
                showToast('Lỗi khi tải danh sách yêu thích', 'error');
                $('#empty-container').show();
            }
        });
    }

    function renderWishlistItems(items) {
        const container = $('#wishlist-items-container');
        container.empty();

        items.forEach(function (item) {
            const itemHtml = `
                        <div class="card bg-white shadow-xl rounded-lg px-3 transition-all duration-200 hover:shadow-2xl fade-in" 
                             id="wishlist-item-${item.product_id}">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 py-3 sm:py-6">
                                <div class="flex items-center gap-3 sm:gap-4 flex-1">
                                    <div class="flex-shrink-0">
                                        <div class="relative h-16 w-16 sm:h-20 sm:w-20 rounded-lg overflow-hidden ring-2 ring-gray-100 hover:ring-blue-300 transition-all duration-200">
                                            <img src="${item.product.image_url}" 
                                                 alt="${item.product.name}"
                                                 class="object-cover w-full h-full hover:scale-110 transition-transform duration-200"
                                                 onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-base sm:text-lg mb-2 line-clamp-2">
                                            ${item.product.name}
                                        </h3>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="${API_ENDPOINTS.cart_add.replace(':id', item.product_id)}" 
                                        method="POST"
                                        class="flex items-center justify-center">
                                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                        <input type="hidden" />
                                        <button type="submit"
                                                class="flex items-center justify-center p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200"
                                                title="Thêm vào giỏ hàng">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" 
                                                    d="M2.25 2.25h1.5l1.5 16.5h12.75l1.5-9H6.75m0 0h13.5m-13.5 0L5.25 6.75h16.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <button class="flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                            onclick="removeFromWishlist('${item.product_id}')"
                                            title="Xóa khỏi danh sách yêu thích">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
            container.append(itemHtml);
        });
    }

    window.removeFromWishlist = function (productId) {
        const url = API_ENDPOINTS.wishlist_remove.replace(':id', productId);

        console.log(productId);

        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    $(`#wishlist-item-${productId}`).fadeOut(300, function () {
                        $(this).remove();
                        checkEmptyWishlist();
                    });
                    showToast(response.message, 'success');
                }
            },
            error: function (response) {
                console.log(response);

                showToast('Lỗi khi xóa sản phẩm', 'error');
            }
        });
    };

    window.clearWishlist = function () {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi danh sách yêu thích?')) {
            return;
        }

        $.ajax({
            url: API_ENDPOINTS.wishlist_clear,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    $('#wishlist-items-container').fadeOut(300, function () {
                        $('#content-container').hide();
                        $('#empty-container').show();
                    });
                    showToast(response.message, 'success');
                }
            },
            error: function () {
                showToast('Lỗi khi xóa danh sách yêu thích', 'error');
            }
        });
    };

    window.updateAllCartItems = function () {
        showToast('Tính năng đang được phát triển', 'info');
    };

    function checkEmptyWishlist() {
        if ($('#wishlist-items-container').children().length === 0) {
            $('#content-container').hide();
            $('#empty-container').show();
        }
    }

    // function showToast(message, type = 'success') {
    //     const isDesktop = $(window).width() >= 640;
    //     const toastSelector = isDesktop ? '#toast' : '#mobile-toast';
    //     const contentSelector = isDesktop ? '#toast-content' : '#mobile-toast-content';
    //     const messageSelector = isDesktop ? '#toast-message' : '#mobile-toast-message';
    //     const iconSelector = isDesktop ? '#toast-icon' : '#mobile-toast-icon';

    //     $(messageSelector).text(message);

    //     const colors = {
    //         success: 'border-green-500',
    //         error: 'border-red-500',
    //         info: 'border-blue-500'
    //     };

    //     $(contentSelector).removeClass('border-green-500 border-red-500 border-blue-500')
    //         .addClass(colors[type] || colors.success);

    //     $(toastSelector).removeClass('hidden');
    //     setTimeout(() => {
    //         $(contentSelector).removeClass(isDesktop ? 'translate-x-full' : 'translate-y-full');
    //     }, 10);
    //     setTimeout(() => {
    //         $(contentSelector).addClass(isDesktop ? 'translate-x-full' : 'translate-y-full');
    //         setTimeout(() => {
    //             $(toastSelector).addClass('hidden');
    //         }, 300);
    //     }, 3000);
    // }
});
