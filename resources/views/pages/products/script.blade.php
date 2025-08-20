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

        @if ($product->type_sale === ($typeSale['AUCTION'] ?? 2) && isset($auctionData['auction']))
        const auctionEndIso =
            "{{ \Carbon\Carbon::parse($auctionData['auction']->end_time)->toIso8601String() }}";
        const targetMs = new Date(auctionEndIso).getTime();
        countdown(targetMs);
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

    function showShareNotification(message) {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        `;

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
