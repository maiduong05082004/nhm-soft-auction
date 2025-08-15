<!-- Toast notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border-l-4 text-gray-700 p-4 rounded shadow-lg" id="toast-content">
        <div class="flex items-center">
            <div class="flex-shrink-0" id="toast-icon">
                <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p id="toast-message" class="text-sm font-medium"></p>
            </div>
        </div>
    </div>
</div>

<style>
    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-btn:disabled:hover {
        background-color: inherit;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }

    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #toast.border-green-500 {
        border-left-color: #10b981;
    }

    #toast.border-red-500 {
        border-left-color: #ef4444;
    }
</style>

<script>
    // Đợi jQuery load xong và DOM ready
    function initializeCart() {
        // Kiểm tra jQuery đã load chưa
        if (typeof $ === 'undefined') {
            console.error('jQuery chưa load! Vui lòng kiểm tra app.js');
            // Thử lại sau 100ms
            setTimeout(initializeCart, 100);
            return;
        }

        console.log('jQuery đã load thành công! Version:', $.fn.jquery);

        // CSRF token cho AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        console.log('CSRF token set:', $('meta[name="csrf-token"]').attr('content'));
    }

    // Khởi tạo khi DOM ready
    document.addEventListener('DOMContentLoaded', initializeCart);

    // Hoặc khởi tạo ngay nếu jQuery đã có sẵn
    if (typeof $ !== 'undefined') {
        initializeCart();
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');

        toastMessage.textContent = message;
        toast.classList.remove('hidden');

        if (type === 'success') {
            toastIcon.innerHTML = '<svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
            toast.classList.add('border-green-500');
        } else {
            toastIcon.innerHTML = '<svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
            toast.classList.add('border-red-500');
        }

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

                    function updateQuantity(productId, newQuantity) {
                    if (newQuantity < 1) return;

                    // Chuyển productId về string để đảm bảo không bị mất số
                    productId = String(productId);

                    // Debug: Kiểm tra product_id và quantity
                    console.log('updateQuantity called with:', { productId, newQuantity, type: typeof productId });

        // Debug: Kiểm tra CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        console.log('CSRF Token for updateQuantity:', csrfToken);

        // Disable tất cả nút quantity trong item này
        const itemContainer = $(`#cart-item-${productId}`);
        const quantityBtns = itemContainer.find('.quantity-btn');
        quantityBtns.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route("cart.update-quantity") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                product_id: productId,
                quantity: newQuantity
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    // Cập nhật UI
                    $(`#quantity-${productId}`).text(response.data.quantity);
                    $(`#item-total-${productId}`).text(new Intl.NumberFormat('vi-VN').format(response.data.total) + ' ₫');

                    // Cập nhật tổng tiền
                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total + 30000) + ' ₫');
                    $('#cart-count').text(response.data.cart_count);

                    // Cập nhật trạng thái nút giảm
                    const minusBtn = itemContainer.find('button[onclick*="' + productId + ', ' + (newQuantity - 1) + '"]');
                    if (newQuantity <= 1) {
                        minusBtn.prop('disabled', true);
                    } else {
                        minusBtn.prop('disabled', false);
                    }

                    // Cập nhật onclick của nút giảm
                    minusBtn.attr('onclick', `updateQuantity(${productId}, ${newQuantity - 1})`);

                    // Cập nhật onclick của nút tăng
                    const plusBtn = itemContainer.find('button[onclick*="' + productId + ', ' + (newQuantity + 1) + '"]');
                    plusBtn.attr('onclick', `updateQuantity(${productId}, ${newQuantity + 1})`);

                    showToast(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                console.error('AJAX Error:', xhr);
                console.error('Response:', response);
                console.error('ProductStatus:', xhr.status);
                console.error('StatusText:', xhr.statusText);

                if (response && response.errors) {
                    console.error('Validation errors:', response.errors);
                    showToast('Lỗi validation: ' + JSON.stringify(response.errors), 'error');
                } else {
                    showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                }
            },
            complete: function() {
                // Re-enable tất cả nút quantity
                quantityBtns.prop('disabled', false).removeClass('loading');
            }
        });
    }

                    function removeItem(productId) {
                    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                        return;
                    }

                    // Chuyển productId về string để đảm bảo không bị mất số
                    productId = String(productId);

                    // Debug: Kiểm tra product_id
                    console.log('removeItem called with:', { productId, type: typeof productId });

        // Debug: Kiểm tra CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        console.log('CSRF Token for removeItem:', csrfToken);

        const itemContainer = $(`#cart-item-${productId}`);
        const removeBtn = itemContainer.find('button[onclick*="removeItem"]');
        removeBtn.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route("cart.remove-item") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                product_id: productId
            },
            success: function(response) {
                console.log('Remove success response:', response);
                if (response.success) {
                    // Xóa item khỏi UI
                    itemContainer.fadeOut(300, function() {
                        $(this).remove();

                        // Kiểm tra nếu giỏ hàng trống
                        if (response.data.cart_count === 0) {
                            location.reload(); // Reload để hiển thị giỏ hàng trống
                        }
                    });

                    // Cập nhật tổng tiền
                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total + 30000) + ' ₫');
                    $('#cart-count').text(response.data.cart_count);

                    showToast(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                console.error('AJAX Error:', xhr);
                console.error('Response:', response);
                showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                removeBtn.prop('disabled', false).removeClass('loading');
            }
        });
    }

    function clearCart() {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
            return;
        }

        // Debug: Kiểm tra CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        console.log('CSRF Token for clearCart:', csrfToken);

        const clearBtn = $('button[onclick="clearCart()"]');
        clearBtn.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route("cart.clear-cart") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload để hiển thị giỏ hàng trống
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                console.error('AJAX Error:', xhr);
                showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                clearBtn.prop('disabled', false).removeClass('loading');
            }
        });
    }
</script>
