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

    function getDaisyToastContainer() {
        let container = document.getElementById('daisy-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'daisy-toast-container';
            container.className = 'toast toast-top toast-end z-50';
            document.body.appendChild(container);
        }
        return container;
    }

    function showToast(message, type = 'success') {
        const container = getDaisyToastContainer();

        const alert = document.createElement('div');
        let cls = 'alert';
        switch (type) {
            case 'success':
                cls += ' alert-success';
                break;
            case 'error':
                cls += ' alert-error';
                break;
            case 'warning':
                cls += ' alert-warning';
                break;
            default:
                cls += ' alert-info';
        }
        alert.className = cls;

        let iconSvg = '';
        if (type === 'success') {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>';
        } else if (type === 'error') {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>';
        } else if (type === 'warning') {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>';
        } else {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }

        alert.innerHTML = iconSvg + '<span>' + message + '</span>';
        container.appendChild(alert);

        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 8000);
    }

    function increaseQuantity(productId) {
        const inputs = document.querySelectorAll(`[id="quantity-input-${productId}"]`);

        const input = inputs[0];
        const currentValue = parseInt(input.value);
        const maxStock = parseInt(input.getAttribute('max'));

        if (currentValue < maxStock) {
            const newQuantity = currentValue + 1;

            inputs.forEach(el => {
                el.value = newQuantity;
            });

            updateLocalPrice(productId, newQuantity);
            checkForChanges();
        } else {
            showToast('Không thể cập nhật sản phẩm vượt quá số lượng tồn kho', 'error');
        }
    }

    function decreaseQuantity(productId) {
        const inputs = document.querySelectorAll(`[id="quantity-input-${productId}"]`);

        const input = inputs[0];
        const currentValue = parseInt(input.value);

        if (currentValue > 1) {
            const newQuantity = currentValue - 1;

            inputs.forEach(el => {
                el.value = newQuantity;
            });

            updateLocalPrice(productId, newQuantity);
            checkForChanges();
        }
    }

    function updateLocalPrice(productId, newQuantity) {
        const itemContainer = document.getElementById(`cart-item-${productId}`);
        if (!itemContainer) return;

        const priceElement = itemContainer.querySelector('.text-green-700');
        if (!priceElement) return;

        const priceText = priceElement.textContent.replace(/[^\d]/g, '');
        const price = parseFloat(priceText);

        const inputs = document.querySelectorAll(`[id="quantity-input-${productId}"]`);
        if (inputs.length === 0) return;

        const maxStock = parseInt(inputs[0].getAttribute('max'));

        if (newQuantity < 1) {
            newQuantity = 1;
            inputs.forEach(el => el.value = 1);
        } else if (newQuantity > maxStock) {
            showToast('Không thể cập nhật sản phẩm vượt quá số lượng tồn kho', 'error');
            newQuantity = maxStock;
            inputs.forEach(el => el.value = maxStock);
        }

        const itemTotal = document.getElementById(`item-total-${productId}`);
        const newItemTotal = price * newQuantity;
        if (itemTotal) {
            itemTotal.textContent = new Intl.NumberFormat('vi-VN').format(newItemTotal) + ' ₫';
        }

        updateCartSummary();

        const allItemContainers = document.querySelectorAll(`[id="cart-item-${productId}"]`);
        allItemContainers.forEach(container => {
            const minusBtn = container.querySelector('button[onclick*="decreaseQuantity"]');
            const plusBtn = container.querySelector('button[onclick*="increaseQuantity"]');
            if (minusBtn) minusBtn.disabled = newQuantity <= 1;
            if (plusBtn) plusBtn.disabled = newQuantity >= maxStock;
        });
    }

    function updateCartSummary() {
        const cartItems = document.querySelectorAll('.card[id^="cart-item-"]');
        let total = 0;
        let count = 0;

        cartItems.forEach(item => {
            const checkbox = item.querySelector('.select-item');
            if (checkbox && !checkbox.checked) return;
            const itemTotal = item.querySelector('[id^="item-total-"]');
            if (itemTotal) {
                const itemTotalText = itemTotal.textContent;
                const itemTotalValue = parseFloat(itemTotalText.replace(/[^\d]/g, ''));
                total += itemTotalValue;
                count++;
            }
        });

        document.getElementById('cart-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        document.getElementById('cart-total').textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        document.getElementById('cart-count').textContent = count;
    }

    function updateAllCartItems() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const updateAllBtn = document.getElementById('update-all-btn');

        updateAllBtn.disabled = true;
        updateAllBtn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Đang cập nhật...';

        const itemsToUpdate = [];
        quantityInputs.forEach(input => {
            const currentQuantity = parseInt(input.value);
            const originalQuantity = parseInt(input.getAttribute('data-original-quantity'));

            if (currentQuantity !== originalQuantity) {
                itemsToUpdate.push({
                    productId: input.getAttribute('data-product-id'),
                    quantity: currentQuantity,
                    originalQuantity: originalQuantity
                });
            }
        });

        if (itemsToUpdate.length === 0) {
            showToast('Không có sản phẩm nào cần cập nhật!', 'info');
            updateAllBtn.disabled = false;
            updateAllBtn.innerHTML = 'Cập nhật tất cả';
            return Promise.resolve();
        }

        const updatePromises = itemsToUpdate.map(item => {
            return new Promise((resolve, reject) => {
                updateQuantity(item.productId, item.quantity, resolve, reject);
            });
        });

        return Promise.all(updatePromises)
            .then(() => {
                showToast(`Đã cập nhật ${itemsToUpdate.length} sản phẩm thành công!`, 'success');

                itemsToUpdate.forEach(item => {
                    const input = document.getElementById(`quantity-input-${item.productId}`);
                    input.setAttribute('data-original-quantity', item.quantity);
                });

                updateAllBtn.style.display = 'none';
            })
            .catch((error) => {
                showToast('Có lỗi xảy ra khi cập nhật!', 'error');
                console.error('Error:', error);
                throw error;
            })
            .finally(() => {
                updateAllBtn.disabled = false;
                updateAllBtn.innerHTML = 'Cập nhật tất cả';
            });
    }

    function updateQuantity(productId, newQuantity, resolve = null, reject = null) {
        if (newQuantity < 1) {
            if (reject) reject(new Error('Số lượng phải lớn hơn 0'));
            return;
        }

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
                    $(`#item-total-${productId}`).text(new Intl.NumberFormat('vi-VN').format(response.data
                        .total) + ' ₫');
                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data
                        .cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total) +
                        ' ₫');
                    $('#cart-count').text(response.data.cart_count);

                    const minusBtn = itemContainer.find('button[onclick*="decreaseQuantity"]');
                    const plusBtn = itemContainer.find('button[onclick*="increaseQuantity"]');

                    minusBtn.prop('disabled', newQuantity <= 1);
                    plusBtn.prop('disabled', newQuantity >= parseInt($(`#quantity-input-${productId}`).attr(
                        'max')));

                    const input = document.getElementById(`quantity-input-${productId}`);
                    input.setAttribute('data-original-quantity', newQuantity);

                    checkForChanges();

                    if (resolve) resolve(response);
                } else {
                    if (reject) reject(new Error(response.message || 'Cập nhật thất bại'));
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = 'Có lỗi xảy ra!';

                if (response && response.message) {
                    if (response.message.includes('vượt quá tồn kho')) {
                        errorMessage = 'Không thể cập nhật sản phẩm vượt quá số lượng tồn kho';
                    } else if (response.message.includes('Số lượng phải lớn hơn 0')) {
                        errorMessage = 'Số lượng phải lớn hơn 0';
                    } else if (response.message.includes('không tồn tại')) {
                        errorMessage = 'Sản phẩm không tồn tại hoặc không khả dụng';
                    } else if (response.message.includes('Không tìm thấy')) {
                        errorMessage = 'Không tìm thấy sản phẩm trong giỏ hàng';
                    } else {
                        errorMessage = response.message;
                    }
                }

                const input = document.getElementById(`quantity-input-${productId}`);
                const originalQuantity = parseInt(input.getAttribute('data-original-quantity'));
                input.value = originalQuantity;
                updateLocalPrice(productId, originalQuantity);

                showToast(errorMessage, 'error');

                if (reject) reject(new Error(errorMessage));
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
                        } else {
                            checkForChanges();
                        }
                    });

                    $('#cart-subtotal').text(new Intl.NumberFormat('vi-VN').format(response.data
                        .cart_total) + ' ₫');
                    $('#cart-total').text(new Intl.NumberFormat('vi-VN').format(response.data.cart_total) +
                        ' ₫');
                    $('#cart-count').text(response.data.cart_total);

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

    function getSelectedProductIds() {
        const selected = [];
        document.querySelectorAll('.select-item:checked').forEach(cb => {
            const pid = cb.getAttribute('data-product-id') || cb.value;
            if (pid) selected.push(pid);
        });
        return selected;
    }

    function saveChangesBeforeCheckout() {
        const hasChanges = checkForChanges();
        const selected = getSelectedProductIds();
        const base = '{{ route('cart.checkout') }}';
        const url = selected.length ? `${base}?selected=${encodeURIComponent(selected.join(','))}` : base;

        if (!hasChanges) {
            window.location.href = url;
            return;
        }

        showSaveChangesModal(url);
    }

    function showSaveChangesModal(checkoutUrl) {
        const modal = document.getElementById('save-changes-modal');
        if (modal) {
            modal.setAttribute('data-checkout-url', checkoutUrl);
            modal.showModal();
        }
    }

    function closeSaveChangesModal() {
        const modal = document.getElementById('save-changes-modal');
        if (modal) {
            modal.close();
        }
    }

    function confirmSaveChanges() {
        const modal = document.getElementById('save-changes-modal');
        const checkoutUrl = modal.getAttribute('data-checkout-url');
        
        if (!checkoutUrl) {
            showToast('Có lỗi xảy ra!', 'error');
            return;
        }

        closeSaveChangesModal();

        updateAllCartItems().then(() => {
            window.location.href = checkoutUrl;
        }).catch(() => {
            showToast('Có lỗi xảy ra khi lưu thay đổi!', 'error');
        });
    }

    function checkForChanges() {
        const updateAllBtn = document.getElementById('update-all-btn');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const updateAllBtnMobile = document.getElementById('mobile-update-all-btn');
        let hasChanges = false;

        quantityInputs.forEach(input => {
            const currentQuantity = parseInt(input.value);
            const originalQuantity = parseInt(input.getAttribute('data-original-quantity'));

            if (currentQuantity !== originalQuantity) {
                hasChanges = true;
            }
        });

        if (hasChanges) {
            updateAllBtn.style.display = 'flex';
            updateAllBtnMobile.classList.remove('hidden');
            updateAllBtnMobile.classList.add('flex');
        } else {
            updateAllBtn.style.display = 'none';
            updateAllBtnMobile.classList.add('hidden');
            updateAllBtnMobile.classList.remove('flex');
        }

        return hasChanges;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(input => {
            input.addEventListener('input', function() {
                const productId = this.getAttribute('data-product-id');
                const newQuantity = parseInt(this.value) || 0;

                const allInputs = document.querySelectorAll(`[id="quantity-input-${productId}"]`);
                allInputs.forEach(el => {
                    if (el !== this) {
                        el.value = this.value;
                    }
                });

                updateLocalPrice(productId, newQuantity);
                checkForChanges();
            });
        });
        const checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCartSummary();
            });
        });
    });

</script>
