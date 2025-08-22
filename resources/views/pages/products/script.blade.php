<script>
    document.addEventListener('DOMContentLoaded', function() {

        @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2) && isset($auctionData['auction']))
        const incrementButtons = document.querySelectorAll('.increment-bid');
        const bidInput = document.getElementById('bid-price');

        if (incrementButtons && bidInput) {
            incrementButtons.forEach((btn, index) => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const increment = parseInt(btn.getAttribute('data-increment'), 10) || 0;
                    const hasBids = {{ $totalBids > 0 ? 'true' : 'false' }};

                    const basePrice = hasBids ?
                        {{ isset($auctionData['current_price']) ? $auctionData['current_price'] : $auctionData['auction']->start_price ?? 0 }} :
                        {{ $auctionData['auction']->start_price ?? 0 }};

                    const newPrice = basePrice + increment;
                    bidInput.value = newPrice;
                    bidInput.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });
            });
        }
        @endif

        function countdown(targetMs) {
            const daysElem = document.getElementById('days');
            const hoursElem = document.getElementById('hours');
            const minutesElem = document.getElementById('minutes');
            const secondsElem = document.getElementById('seconds');

            function setValue(el, val) {
                if (!el) return;
                const safe = Math.max(0, val);
                el.style.setProperty('--value', safe);
                el.setAttribute('aria-label', String(safe));
            }

            function updateCountdown() {
                const nowMs = Date.now();
                const timeLeft = targetMs - nowMs;

                if (timeLeft <= 0) {
                    setValue(daysElem, 0);
                    setValue(hoursElem, 0);
                    setValue(minutesElem, 0);
                    setValue(secondsElem, 0);
                    clearInterval(interval);
                    if (typeof handleAuctionEnded === 'function') {
                        handleAuctionEnded();
                    }
                    return;
                }

                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                setValue(daysElem, days);
                setValue(hoursElem, hours);
                setValue(minutesElem, minutes);
                setValue(secondsElem, seconds);
            }

            const interval = setInterval(updateCountdown, 1000);
            updateCountdown();
        }

        function userBidCountdown(targetMs) {
            console.log('userBidCountdown called with targetMs:', targetMs);
            const countdownElem = document.getElementById('user-bid-countdown');
            if (!countdownElem) {
                console.error('Countdown element not found');
                return;
            }

            const spans = countdownElem.querySelectorAll('span');
            if (spans.length < 3) {
                console.error('Not enough spans found:', spans.length);
                return;
            }
            
            console.log('Countdown element and spans found, starting countdown');

            function setValue(el, val) {
                if (!el) return;
                const safe = Math.max(0, val);
                el.style.setProperty('--value', safe);
                el.setAttribute('aria-label', String(safe));
                el.textContent = String(safe).padStart(2, '0');
            }

            function updateUserBidCountdown() {
                const nowMs = Date.now();
                const timeLeft = targetMs - nowMs;

                if (timeLeft <= 0) {
                    setValue(spans[0], 0);
                    setValue(spans[1], 0);
                    setValue(spans[2], 0);
                    clearInterval(interval);
                    
                    countdownElem.style.display = 'none';
                    showBidAvailableNotification();
                    return;
                }

                const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                setValue(spans[0], hours);
                setValue(spans[1], minutes);
                setValue(spans[2], seconds);
            }

            const interval = setInterval(updateUserBidCountdown, 1000);
            updateUserBidCountdown();
        }

        @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2) && isset($auctionData['auction']))
        const auctionEndIso =
            "{{ \Carbon\Carbon::parse($auctionData['auction']->end_time)->toIso8601String() }}";
        const targetMs = new Date(auctionEndIso).getTime();
        countdown(targetMs);
        
        @if (auth()->check() && isset($userBidInfo) && $userBidInfo['success'] && !$userBidInfo['can_bid_now'])
        const userBidNextTime = "{{ $userBidInfo['next_bid_time'] ?? '' }}";
        console.log('User bid info:', {!! json_encode($userBidInfo ?? null) !!});
        console.log('User bid next time:', userBidNextTime);
        if (userBidNextTime && userBidNextTime !== '') {
            const userBidTargetMs = new Date(userBidNextTime).getTime();
            console.log('Starting user bid countdown for:', userBidNextTime, 'Target ms:', userBidTargetMs);
            if (!isNaN(userBidTargetMs)) {
                userBidCountdown(userBidTargetMs);
            } else {
                console.error('Invalid date format:', userBidNextTime);
            }
        } else {
            console.log('No next bid time available');
        }
        @endif
        @endif
    });

    function showTab(tabId) {
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => {
            content.style.display = 'none';
        });

        const tabs = document.querySelectorAll('[onclick^="showTab"]');
        tabs.forEach(tab => {
            tab.classList.remove('border-blue-500');
            tab.classList.add('border-transparent');
        });

        const selectedContent = document.getElementById(tabId);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }

        const activeTab = document.querySelector(`[onclick="showTab('${tabId}')"]`);
        if (activeTab) {
            activeTab.classList.remove('border-transparent');
            activeTab.classList.add('border-blue-500');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        showTab('description');
    });

    function decreaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        const minValue = parseInt(quantityInput.min);

        if (currentValue > minValue) {
            quantityInput.value = currentValue - 1;
        }
    }

    function increaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.max);

        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
        }
    }

    function shareProduct() {
        const currentUrl = window.location.href;
        const productName = "{{ $product->name }}";

        if (navigator.share) {
            navigator.share({
                title: productName,
                text: `Xem sản phẩm: ${productName}`,
                url: currentUrl
            }).catch((error) => {
                copyToClipboard(currentUrl);
            });
        } else {
            copyToClipboard(currentUrl);
        }
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showShareNotification('Đã sao chép link vào clipboard!');
            }).catch(() => {
                fallbackCopyToClipboard(text);
            });
        } else {
            fallbackCopyToClipboard(text);
        }
    }

    function fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            showShareNotification('Đã sao chép link vào clipboard!');
        } catch (err) {
            showShareNotification('Không thể sao chép link!');
        }

        document.body.removeChild(textArea);
    }

    let bidConfirmed = false;

    function showBidConfirmation(e) {
        if (e) e.preventDefault();
        
        const endedAlert = document.getElementById('auction-ended-alert');
        if (endedAlert && !endedAlert.classList.contains('hidden')) {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast toast-top toast-end z-50';
                document.body.appendChild(toastContainer);
            }
            const toast = document.createElement('div');
            toast.className = 'alert alert-warning shadow-lg';
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="stroke-current shrink-0 h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Phiên đấu giá đã kết thúc, không thể đấu giá thêm!</span>
            `;
            toastContainer.appendChild(toast);
            setTimeout(()=>{ if (toast.parentNode) toast.parentNode.removeChild(toast); }, 4000);
            return;
        }

        const countdownElem = document.getElementById('user-bid-countdown');
        if (countdownElem && countdownElem.style.display !== 'none') {
            showBidDelayError();
            return;
        }
        
        const modal = document.getElementById('bid_confirmation_modal');
        if (modal) {
            modal.showModal();
        }
    }

    function closeBidConfirmation() {
        const modal = document.getElementById('bid_confirmation_modal');
        if (modal) {
            modal.close();
        }
    }

    function confirmBid() {
        const bidForm = document.getElementById('bid-form');
        if (bidForm) {
            closeBidConfirmation();
            bidConfirmed = true;
            bidForm.submit();
        }
    }

    (function attachBidFormGuard(){
        const bidForm = document.getElementById('bid-form');
        if (!bidForm) return;
        bidForm.addEventListener('submit', function(ev){
            if (!bidConfirmed) {
                ev.preventDefault();
                showBidConfirmation();
            }
        });
    })();

    function handleAuctionEnded() {
        const wrapper = document.getElementById('auction-bid-wrapper');
        if (wrapper) {
            wrapper.innerHTML = '<button class="btn btn-disabled w-full" disabled>Đấu giá ngay</button>';
        }
        const ended = document.getElementById('auction-ended-alert');
        if (ended) {
            ended.classList.remove('hidden');
        }
    }

    function showBidDelayError() {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast toast-top toast-end z-50';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'alert alert-error shadow-lg';
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Bạn cần đợi hết thời gian đếm ngược mới có thể đấu giá tiếp!</span>
        `;
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }

    function showBidAvailableNotification() {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast toast-top toast-end z-50';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'alert alert-success shadow-lg';
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Bây giờ bạn có thể đấu giá tiếp!</span>
        `;
        
        toastContainer.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }

    function showShareNotification(message) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast toast-top toast-end z-50';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'alert alert-info shadow-lg';
        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        `;
        
        toastContainer.appendChild(toast);
        
            setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }
</script>

<script>
    (function attachAddToCartAjax(){
        const $form = $('#add-to-cart-form');
        if ($form.length === 0) return;

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' }
        });

        function setCartBadge(next) {
            const $badge = $('#header-cart-count');
            $badge.text(Number.isInteger(next) ? next : 0);
            if (Number(next) > 0) $badge.removeClass('hidden');
        }

        function incCartBadge(delta) {
            const $badge = $('#header-cart-count');
            const cur = parseInt(($badge.text() || '0').trim(), 10) || 0;
            setCartBadge(Math.max(0, cur + delta));
        }

        $form.on('submit', function(e){
            e.preventDefault();
            const $this = $(this);
            const url = $this.attr('action');
            const qty = parseInt(($('#quantity').val() || '1'), 10) || 1;

            $.ajax({
                url: url,
                method: 'POST',
                data: $this.serialize(),
                dataType: 'json'
            }).done(function(resp){
                if (resp && resp.success && resp.data && Number.isInteger(resp.data.count)) {
                    setCartBadge(resp.data.count);
                } else {
                    incCartBadge(qty);
                }
                if (typeof showToast === 'function') {
                    showToast(resp && resp.message ? resp.message : 'Đã thêm vào giỏ hàng', 'success');
                }
            }).fail(function(){
                $this.off('submit');
                $this.trigger('submit');
            });
        });
    })();
</script>
