<?php

namespace App\Http\Controllers;

use App\Services\Cart\CartServiceInterface;
use App\Services\Orders\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;
    protected $orderService;

    public function __construct(CartServiceInterface $cartService, OrderServiceInterface $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function addToCart(Request $request, $productId)
    {
        $quantity = $request->input('quantity', 1);
        $userId = Auth::id();

        $result = $this->cartService->addToCart($userId, $productId, $quantity);

        if ($result['success']) {
            return redirect()->route('cart.index')->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function index()
    {
        $userId = Auth::id();
        $result = $this->cartService->getUserCart($userId);

        if ($result['success']) {
            $cartItems = $result['data']['cartItems'];
            $total = $result['data']['total'];
            return view('pages.cart.cart', compact('cartItems', 'total'));
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }


    public function checkout()
    {
        $userId = Auth::id();
        $result = $this->cartService->getUserCart($userId);

        if ($result['success']) {
            $cartItems = $result['data']['cartItems'];
            $total = $result['data']['total'];
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
            }

            return view('pages.checkout.checkout', compact('cartItems', 'total'));
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:0,1',
        ]);
        $userId = Auth::id();
        $checkoutData = [
            'address' => $request->address,
            'email' => $request->email,
            'payment_method' => $request->payment_method,
            'note' => $request->note ?? ''
        ];

        $result = $this->orderService->processCheckout($userId, $checkoutData);

        if ($result['success']) {
            if ($checkoutData['payment_method'] == '1') {
                return redirect()->route('payment.qr', $result['data']['order_id']);
            }
            return redirect()->route('order.success', $result['data']['order_id']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function qrPayment($orderId)
    {
        $result = $this->orderService->getOrderDetails($orderId);
        
        if ($result['success']) {
            $orderDetail = $result['data']['orderDetail'];
            $payment = $result['data']['payment'];
            return view('filament.admin.resources.orders.qr-code', compact('orderDetail', 'payment'));
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function confirmPayment($orderId)
    {
        $result = $this->orderService->confirmPayment($orderId);
        
        if ($result['success']) {
            return redirect()->route('order.success', $orderId);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function orderSuccess($orderId)
    {
        $result = $this->orderService->getOrderDetails($orderId);
        
        if ($result['success']) {
            $orderDetail = $result['data']['orderDetail'];
            return view('pages.checkout.success', compact('orderDetail'));
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        $userId = Auth::id();
        $result = $this->cartService->updateQuantity($userId, $request->product_id, $request->quantity);
        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $result = $this->cartService->removeItem($userId, $request->product_id);
        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    public function clearCart()
    {
        $userId = Auth::id();
        $result = $this->cartService->clearCart($userId);

        if ($result['success']) {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }
}
