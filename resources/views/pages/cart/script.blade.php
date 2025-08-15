<script>
    function initializeCart() {
        if (typeof $ === 'undefined') {
            setTimeout(initializeCart, 100);
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initializeCart);

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
            toastIcon.innerHTML =
                '<svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
            toast.classList.add('border-green-500');
        } else {
            toastIcon.innerHTML =
                '<svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
            toast.classList.add('border-red-500');
        }

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) return;

        productId = String(productId);

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const itemContainer = $(`#cart-item-${productId}`);
        const quantityBtns = itemContainer.find('.quantity-btn');
        quantityBtns.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route('cart.update-quantity') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                product_id: productId,
                quantity: newQuantity
            },
            success: function(response) {
                if (response.success) {
                    $(`#quantity-${productId}`).text(response.data.quantity);
                    $(`#item-total-${productId}`).text(new Intl.NumberFormat('vi-VN').format(response.data
                        .total) + ' ₫');

                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data
                        .cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total +
                        30000) + ' ₫');
                    $('#cart-count').text(response.data.cart_count);

                    const minusBtn = itemContainer.find('button[onclick*="' + productId + ', ' + (
                        newQuantity - 1) + '"]');
                    if (newQuantity <= 1) {
                        minusBtn.prop('disabled', true);
                    } else {
                        minusBtn.prop('disabled', false);
                    }

                    minusBtn.attr('onclick', `updateQuantity(${productId}, ${newQuantity - 1})`);

                    const plusBtn = itemContainer.find('button[onclick*="' + productId + ', ' + (
                        newQuantity + 1) + '"]');
                    plusBtn.attr('onclick', `updateQuantity(${productId}, ${newQuantity + 1})`);

                    showToast(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    console.error('Validation errors:', response.errors);
                    showToast('Lỗi validation: ' + JSON.stringify(response.errors), 'error');
                } else {
                    showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                }
            },
            complete: function() {
                quantityBtns.prop('disabled', false).removeClass('loading');
            }
        });
    }

    function removeItem(productId) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            return;
        }

        productId = String(productId);

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const itemContainer = $(`#cart-item-${productId}`);
        const removeBtn = itemContainer.find('button[onclick*="removeItem"]');
        removeBtn.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route('cart.remove-item') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    itemContainer.fadeOut(300, function() {
                        $(this).remove();

                        if (response.data.cart_count === 0) {
                            location.reload();
                        }
                    });

                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data
                        .cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total +
                        30000) + ' ₫');
                    $('#cart-count').text(response.data.cart_count);

                    showToast(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                removeBtn.prop('disabled', false).removeClass('loading');
            }
        });
    }

    function clearCart() {
        if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const clearBtn = $('button[onclick="clearCart()"]');
        clearBtn.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '{{ route('cart.clear-cart') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast(response?.message || 'Có lỗi xảy ra!', 'error');
                clearBtn.prop('disabled', false).removeClass('loading');
            }
        });
    }
</script>
