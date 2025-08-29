<div class="toast toast-top toast-end" id="auction-success-toast" style="display: none;">
    <div class="alert alert-success border-2 border-[#b9f8cf] bg-[#f0fdf4] shadow-xl max-w-md backdrop-blur-sm">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center">
                    <img src="{{ asset('images/svg/circle-check-big.svg') }}" alt="Thành công" class="w-6 h-6">
                </div>
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-lg font-bold text-green-800 mb-2">Đấu giá thành công!</h4>
                
                <div class="space-y-2 text-sm text-green-700 mb-2">
                    <div class="flex items-start">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full mt-2 mr-2 flex-shrink-0"></div>
                        <span>Bạn đang dẫn đầu cuộc đấu giá này</span>
                    </div>
                    <div class="flex items-start">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full mt-2 mr-2 flex-shrink-0"></div>
                        <span>Chúng tôi sẽ thông báo nếu có người đấu giá cao hơn</span>
                    </div>
                    <div class="flex items-start mb-2">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full mt-2 mr-2 flex-shrink-0"></div>
                        <span>Nhớ theo dõi thời gian kết thúc đấu giá</span>
                    </div>
                </div>
            </div>
            <button onclick="hideAuctionSuccessToast()" class="btn btn-sm btn-ghost text-green-600 hover:bg-green-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function showAuctionSuccessToast() {
    const toast = document.getElementById('auction-success-toast');
    if (toast) {
        toast.style.display = 'block';
        
        setTimeout(() => {
            hideAuctionSuccessToast();
        }, 8000);
    }
}

function hideAuctionSuccessToast() {
    const toast = document.getElementById('auction-success-toast');
    if (toast) {
        toast.style.display = 'none';
    }
}
</script>
