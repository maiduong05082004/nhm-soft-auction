<div class="space-y-6">
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Thông tin đơn hàng</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700">Mã đơn hàng:</span>
                <span class="ml-2 text-gray-900">{{ $order->code_orders }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Khách hàng:</span>
                <span class="ml-2 text-gray-900">{{ $order->user->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Tổng tiền:</span>
                <span class="ml-2 text-gray-900 font-semibold">{{ number_format($order->total, 0, ',', '.') }} ₫</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Ngày đặt:</span>
                <span class="ml-2 text-gray-900">{{ $order->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-lg">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Trạng thái thanh toán</h3>
        </div>
        
        @if($payment)
            <div class="p-4">
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <span class="font-medium text-gray-700">Phương thức:</span>
                        <span class="ml-2 text-gray-900">
                            @if($payment->payment_method == '0')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Giao dịch trực tiếp
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Chuyển khoản ngân hàng
                                </span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Số tiền:</span>
                        <span class="ml-2 text-gray-900 font-semibold">{{ number_format($payment->amount, 0, ',', '.') }} ₫</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Trạng thái:</span>
                        <span class="ml-2">
                            @if($payment->status === 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Đã thanh toán
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Chờ xác nhận
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Ngày thanh toán:</span>
                        <span class="ml-2 text-gray-900">
                            @if($payment->pay_date)
                                {{ \Carbon\Carbon::parse($payment->pay_date)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-500">Chưa thanh toán</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Người bán xác nhận:</span>
                        <span class="ml-2 text-gray-900">
                            @if(!empty($payment->confirmation_at))
                                Lúc {{ \Carbon\Carbon::parse($payment->confirmation_at)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-500">Chưa xác nhận</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($payment->status === 'pending' && $isAdmin)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-3">Xác nhận thanh toán</h4>
                        <p class="text-sm text-gray-600 mb-4">
                            Xác nhận rằng bạn đã nhận được tiền từ khách hàng và cập nhật trạng thái thanh toán.
                        </p>
                        <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Xác nhận đã nhận tiền
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có thông tin thanh toán</h3>
                <p class="mt-1 text-sm text-gray-500">Đơn hàng này chưa có thông tin thanh toán.</p>
            </div>
        @endif
    </div>
</div>