<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bidForm = document.getElementById('bid-form');

        if (bidForm) {
            bidForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const bidPrice = document.getElementById('bid-price').value;
                const productId = {{ $product->id }};

                const submitBtn = bidForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang xử lý...';

                fetch(`/auctions/${productId}/bid`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').
                                getAttribute('content')
                        },
                        body: JSON.stringify({
                            bid_price: bidPrice
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi đặt giá thầu!');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    });
            });
        }

        @if($product->type_sale === ($typeSale['AUCTION'] ?? 2) && isset($auctionData['auction']))
        const incrementButtons = document.querySelectorAll('.increment-bid');
        const bidInput = document.getElementById('bid-price');

        if (incrementButtons && bidInput) {
            incrementButtons.forEach((btn, index) => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const increment = parseInt(btn.getAttribute('data-increment'), 10) || 0;
                    const hasBids = {{ $totalBids > 0 ? 'true' : 'false' }};

                    const basePrice = hasBids
                        ? {{ isset($auctionData['current_price']) ? $auctionData['current_price'] : ($auctionData['auction']->start_price ?? 0) }}
                        : {{ $auctionData['auction']->start_price ?? 0 }};

                    const newPrice = basePrice + increment;
                    bidInput.value = newPrice;
                    bidInput.dispatchEvent(new Event('input', { bubbles: true }));
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

        @if($product->type_sale === ($typeSale['AUCTION'] ?? 2) && isset($auctionData['auction']))
            const auctionEndIso = "{{ \Carbon\Carbon::parse($auctionData['auction']->end_time)->toIso8601String() }}";
            console.log('[Auction] end_time ISO:', auctionEndIso);
            const targetMs = new Date(auctionEndIso).getTime();
            console.log('[Auction] countdown targetMs:', targetMs, '->', new Date(targetMs).toString());
            countdown(targetMs);
        @else
            console.log('[Auction] Sản phẩm không phải đấu giá hoặc không có dữ liệu phiên đấu giá. Bỏ qua countdown/bid increment.');
        @endif
    });
</script>
