@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Thanh toán</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6">Thông tin thanh toán</h2>
            
            <form action="{{ route('cart.process') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Họ tên</label>
                    <input type="text" name="name" required class="input input-bordered w-full">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" required class="input input-bordered w-full">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Số điện thoại</label>
                    <input type="text" name="phone" required class="input input-bordered w-full">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Địa chỉ giao hàng</label>
                    <textarea name="address" required rows="3" class="textarea textarea-bordered w-full"></textarea>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Phương thức thanh toán</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="0" checked class="radio">
                            <span class="ml-2">Giao dịch trực tiếp</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="1" class="radio">
                            <span class="ml-2">Chuyển khoản ngân hàng</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-full">Đặt hàng</button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6">Tóm tắt đơn hàng</h2>
            
            @foreach($cartItems as $cartItem)
            <div class="flex items-center py-3 border-b">
                <img src="{{ $cartItem->product->images->first()->image_url ? asset('storage/' . $cartItem->product->images->first()->image_url) : asset('images/default-avatar.png') }}" 
                     alt="{{ $cartItem->product->name }}" 
                     class="w-16 h-16 object-cover rounded">
                
                <div class="ml-3 flex-1">
                    <h4 class="font-medium">{{ $cartItem->product->name }}</h4>
                    <p class="text-sm text-gray-600">Số lượng: {{ $cartItem->quantity }}</p>
                </div>
                
                <p class="font-semibold">{{ number_format($cartItem->product->price * $cartItem->quantity, 0, ',', '.') }} ₫</p>
            </div>
            @endforeach
            
            <div class="mt-6 space-y-2">
                <div class="flex justify-between">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                </div>
                <div class="flex justify-between">
                    <span>Phí vận chuyển:</span>
                    <span>Miến phí</span>
                </div>
                <div class="flex justify-between font-semibold text-lg border-t pt-2">
                    <span>Tổng cộng:</span>
                    <span>{{ number_format($total + 30000, 0, ',', '.') }} ₫</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection