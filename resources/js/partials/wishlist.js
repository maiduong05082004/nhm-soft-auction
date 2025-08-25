(() => {
    function normalizeIds(obj) {
        if (Array.isArray(obj)) {
            obj.forEach(i => normalizeIds(i));
            return;
        }
        if (obj && typeof obj === 'object') {
            Object.keys(obj).forEach(k => {
                const v = obj[k];
                if (v && typeof v === 'object') {
                    normalizeIds(v);
                    return;
                }
                if (/(^id$)|(_id$)/i.test(k) && v !== null && v !== undefined) {
                    obj[k] = String(v);
                }
            });
        }
    }
    const $config = $('#config-route');
    if ($config.length === 0) {
        console.error('Missing #config-route element with data attributes for endpoints');
        return;
    }

    const API = {
        wishlist_get: $config.data('wishlist-get'),
        wishlist_add: $config.data('wishlist-add'),
        wishlist_remove: $config.data('wishlist-remove'),
        wishlist_clear: $config.data('wishlist-clear'),
        cart_add: $config.data('cart-add'),
        product_detail: $config.data('product-detail'),
        home: $config.data('home')
    };

    const DEFAULT_IMAGE = '/images/default-avatar.png';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || ''
        }
    });

    const $wishlistCount = $('#wishlist-count');
    const $container = $('#wishlist-items-container');
    const $loading = $('#loading-container');
    const $content = $('#content-container');
    const $empty = $('#empty-container');
    const $actionButtons = $('#action-buttons');
    const $clearButtons = $('#btn-clear-wishlist');
    const $mobileActionButtons = $('#mobile-action-buttons');

    const setWishlistCount = (n = 0) => {
        const next = Number.isInteger(n) ? n : 0;
        $wishlistCount.text(next);
        if ($wishlistCount.length) {
            if (next > 0) {
                $wishlistCount.removeClass('hidden');
            } else {
                $wishlistCount.addClass('hidden');
            }
        }
    };

    const incrementWishlistCount = (delta = 1) => {
        const cur = parseInt($wishlistCount.text() || '0', 10);
        setWishlistCount(Math.max(0, cur + delta));
    };

    function isUnauth(obj) {
        if (!obj) return false;
        if (obj.status && Number(obj.status) === 401) return true;
        if (obj.response && obj.response.status && Number(obj.response.status) === 401) return true;
        const msg = (obj.message || (obj.response && obj.responseJSON && obj.responseJSON.message) || (obj.response && obj.response.data && obj.response.data.message));
        if (typeof msg === 'string' && /unauthenticated\.?/i.test(msg.trim())) return true;
        const errMsg = (obj.error || (obj.response && obj.responseJSON && obj.responseJSON.error));
        if (typeof errMsg === 'string' && /unauthenticated\.?/i.test(errMsg.trim())) return true;
        return false;
    }

    const showToastSafe = (msg, type = 'info') => {
        let text = msg;
        if (msg && typeof msg === 'object' && !(msg instanceof String)) {
            text = msg.message || (msg.responseJSON && msg.responseJSON.message) || (msg.response && msg.response.data && msg.response.data.message) || '';
        }
        if (typeof text === 'string' && /unauthenticated\.?/i.test(text.trim())) {
            return;
        }
        if (!text) return;
        if (typeof showToast === 'function') {
            showToast(text, type);
        } else {
            console[type === 'error' ? 'error' : 'log'](text);
        }
    };

    const safeImgAttr = src => (src ? src : DEFAULT_IMAGE);
    function renderWishlistItems(items) {
        $container.empty();
        console.log(items);

        if (!Array.isArray(items) || items.length === 0) {
            $content.hide();
            $empty.show();
            $clearButtons.hide();
            setWishlistCount(0);
            return;
        }

        const DEFAULT_IMG = API.home + '/images/product_default.jpg'; // thay nếu cần
        function formatPrice(value) {
            if (!value && value !== 0) return '0 ₫';
            try {
                return new Intl.NumberFormat('vi-VN').format(Number(value)) + ' ₫';
            } catch (e) {
                return value + ' ₫';
            }
        }

        items.forEach(item => {
            const product = item.product || {};
            const pid = String(item.product_id || product.id || '').trim();
            console.log(pid);

            const productSlug = product.slug || pid;
            const productUrl = API.product_detail.replace(':id', encodeURIComponent(productSlug));
            const imageUrl = product.first_image || null
                ? (API.home + '/file/' + product.first_image.image_url )
                : DEFAULT_IMG;

            const name = product.name || 'Sản phẩm';
            const typeSale = Number(product.type_sale || 0);
            console.log(typeSale);

            const addToCartHtml = typeSale === 1
                ? `<form action="${API.cart_add.replace(':id', pid)}" method="POST" class="add-cart-form">
                <input type="hidden" name="_token" value="${$('meta[name=\"csrf-token\"]').attr('content')}">
                <input type="hidden" name="product_id" value="${pid}">
                <button type="submit"
                    class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                    title="Thêm vào giỏ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4.01" />
                    </svg>
                </button>
            </form>`
                : `<button type="button" disabled class="flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-50 rounded-lg cursor-not-allowed" title="Không thể thêm vào giỏ"> 
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13" />
              </svg>
           </button>`;

            const priceHtml = product.price
                ? `<div class="text-sm font-bold text-green-600">${formatPrice(product.price)}</div>`
                : (product.min_bid_amount && product.max_bid_amount
                    ? `<div class="text-xs font-bold text-green-600">${formatPrice(product.min_bid_amount)} - ${formatPrice(product.max_bid_amount)}</div>`
                    : `<div class="text-sm font-bold text-green-600">0 ₫</div>`);

            const itemHtml = `
        <div class="bg-[#f5f5f5] rounded-lg border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-all duration-300" id="wishlist-item-${pid}">
            <div class="relative overflow-hidden bg-gray-50">
                <a href="${productUrl}" class="block aspect-square">
                    <img src="${imageUrl}" alt="${escapeHtml(name)}" 
                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                        onerror="this.src='${DEFAULT_IMG}'">
                </a>
            </div>
            
            <div class="p-3">
                <h3 class="font-medium text-sm text-gray-900 mb-2 line-clamp-2 leading-tight min-h-9 " style="min-height: 38px">
                    <a href="${productUrl}" class="hover:text-blue-600 transition-colors">${escapeHtml(name)}</a>
                </h3>
                
                <div class="mb-3">
                    ${priceHtml}
                </div>
                
                <div class="flex items-center justify-between gap-2">
                    <a href="${productUrl}"
                        class="flex items-center justify-center flex-1 h-8 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all duration-200 text-xs font-medium"
                        title="Xem sản phẩm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Xem
                    </a>
                    
                    ${addToCartHtml}
                     <button type="button" data-id="${pid}" 
                        class="remove-wishlist-btn flex items-center justify-center w-10 h-10 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all duration-200"
                        title="Xóa khỏi danh sách yêu thích" aria-label="Xóa">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1h3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        `;

            $container.append(itemHtml);
        });

        setWishlistCount(items.length);
        $content.show();
        $empty.hide();
        $clearButtons.show();
    }
    function escapeHtml(str) {
        if (typeof str !== 'string') return '';
        return str.replace(/[&<>"']/g, tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[tag]));
    }

    const AUTH_MESSAGE = 'Bạn phải đăng nhập để sử dụng chức năng này.';

    async function loadWishlistItems() {
        try {
            $loading.show();
            const resp = await $.ajax({ url: API.wishlist_get, method: 'GET', dataType: 'json' });
            $loading.hide();

            if (isUnauth(resp)) {
                $content.hide();
                $actionButtons.hide();
                $mobileActionButtons.hide();
                $clearButtons.hide();
                $empty.show();
                setWishlistCount(0);
                return;
            }

            if (resp && resp.success && Array.isArray(resp.data) && resp.data.length > 0) {
                normalizeIds(resp.data);

                renderWishlistItems(resp.data);
                setWishlistCount(resp.data.length);
                $content.show();
                $actionButtons.show();
                $mobileActionButtons.show();
                $clearButtons.show();
                $empty.hide();
            } else {
                $clearButtons.hide();
                $content.hide();
                $empty.show();
                setWishlistCount(0);
            }

        } catch (err) {
            $loading.hide();
            $content.hide();
            $clearButtons.hide();
            $empty.show();
            console.error(err);
        }
    }

    async function addWishlistItem(productId, $triggerBtn) {
        if (!productId) return;
        try {
            $triggerBtn && $triggerBtn.prop('disabled', true);

            const resp = await $.ajax({
                url: API.wishlist_add,
                method: 'POST',
                dataType: 'json',
                data: { product_id: productId }
            });

            if (isUnauth(resp)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                $triggerBtn && $triggerBtn.prop('disabled', false);
                return;
            }

            if (resp && resp.success) {
                if (Array.isArray(resp.data)) {
                    setWishlistCount(resp.data.length);
                } else {
                    incrementWishlistCount(1);
                }
                showToastSafe('Đã thêm vào danh sách yêu thích', 'success');
            } else {
                showToastSafe('Thêm thất bại', 'error');
            }
        } catch (err) {
            console.error(err);
            if (isUnauth(err)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                return;
            }
            console.log(err);
            
            showToastSafe(  err.responseJSON.message ||'Lỗi khi thêm danh sách yêu thích', 'error');
        } finally {
            $triggerBtn && $triggerBtn.prop('disabled', false);
        }
    }

    async function removeWishlistItem(productId) {
        if (!productId) return;
        console.log(productId);

        try {
            const resp = await $.ajax({
                url: API.wishlist_remove,
                data: {
                    product_id: productId
                },
                method: 'DELETE',
                dataType: 'json'
            });

            if (isUnauth(resp)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                return;
            }

            if (resp && resp.success) {
                $(`#wishlist-item-${productId}`).fadeOut(300, function () {
                    $(this).remove();
                    const cur = parseInt($wishlistCount.text() || '0', 10);
                    setWishlistCount(Math.max(0, cur - 1));
                    if ($container.children().length === 0) {
                        $content.hide();
                        $empty.show();
                    }
                });
                showToastSafe(resp.message || 'Đã xóa', 'success');
            } else {
                showToastSafe(resp.message || 'Xóa thất bại', 'error');
            }
        } catch (err) {
            console.error(err);
            if (isUnauth(err)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                return;
            }
            showToastSafe( err.message ||'Lỗi khi xóa sản phẩm', 'error');
        }
    }

    async function clearWishlist() {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi danh sách yêu thích?')) return;
        try {
            const resp = await $.ajax({ url: API.wishlist_clear, method: 'DELETE' });

            if (isUnauth(resp)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                return;
            }

            if (resp && resp.success) {
                $container.fadeOut(300, () => {
                    $container.empty();
                    $content.hide();
                    $empty.show();
                    setWishlistCount(0);
                });
                showToastSafe(resp.message || 'Đã xóa tất cả', 'success');
            } else {
                showToastSafe(resp.message || 'Xóa thất bại', 'error');
            }
        } catch (err) {
            console.error(err);
            if (isUnauth(err)) {
                showToastSafe(AUTH_MESSAGE, 'info');
                return;
            }
            showToastSafe( err.message ||'Lỗi khi xóa danh sách yêu thích', 'error');
        }
    }
    $(document).on('click', '.wishlist-btn', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const pid = $btn.data('id') || $btn.attr('data-id');
        addWishlistItem(pid, $btn);
    });
    $(document).on('click', '.remove-wishlist-btn', function (e) {
        e.preventDefault();
        const pid = $(this).attr('data-id')?.trim();
        removeWishlistItem(pid);
    });
    $(document).on('click', '#btn-clear-wishlist', function (e) {
        e.preventDefault();
        clearWishlist();
    });
    $(function () {
        loadWishlistItems();
    });

})();
