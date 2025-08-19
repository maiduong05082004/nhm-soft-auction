<?php

namespace App\Http\Controllers;

use App\Services\Cart\CartServiceInterface;
use App\Services\Checkout\CheckoutServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditCard;

class CartController extends Controller
{
    protected $cartService;
    protected $checkoutService;

    public function __construct(CartServiceInterface $cartService, CheckoutServiceInterface $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
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
            if (request()->filled('selected')) {
                $selected = collect(explode(',', request('selected')))->filter()->map(fn($v) => (int) $v)->all();
                if (!empty($selected)) {
                    $cartItems = $cartItems->whereIn('product_id', $selected);
                    $total = $cartItems->sum(fn($i) => ($i->price ?? 0) * ($i->quantity ?? 0));
                }
            }
            
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
            'selected' => 'nullable|string'
        ]);
        $userId = Auth::id();
        $checkoutData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'payment_method' => $request->payment_method,
            'note' => $request->note ?? '',
            'selected' => $request->selected
        ];
        $result = $this->checkoutService->processCheckout($userId, $checkoutData);
        if ($result['success']) {
            if ($checkoutData['payment_method'] == '1') {
                return redirect()->route('payment.qr', ['order' => $result['data']['order_detail_id']]);
            }

            return $this->orderSuccess($result['data']['order_detail_id']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function qrPayment($orderId)
    {
        $result = $this->checkoutService->getOrderDetails($orderId);
        
        if ($result['success']) {
            $orderDetail = $result['data']['orderDetail'];
            $payment = $result['data']['payment'];
            $creditCard = CreditCard::first();
            if (!$creditCard) {
                return redirect()->route('cart.index')->with('error', 'Thiếu cấu hình thẻ ngân hàng để tạo QR.');
            }

            $vietqrUrl = 'https://img.vietqr.io/image/' . $creditCard->bank . '-' . $creditCard->card_number . '-compact2.jpg';
            $vietqrUrl .= '?amount=' . ($payment->amount ?? 0);
            $vietqrUrl .= '&addInfo=' . urlencode('Thanh toan don hang ' . ($orderDetail->code_orders ?? ''));
            $vietqrUrl .= '&accountName=' . urlencode($creditCard->name);

            return view('pages.checkout.payment', compact('orderDetail', 'payment', 'vietqrUrl'));
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function confirmPayment($orderId)
    {
        $result = $this->checkoutService->confirmPayment($orderId);
        if ($result['success']) {
            $detail = $this->checkoutService->getOrderDetails($orderId);
            if ($detail['success']) {
                $orderDetail = $detail['data']['orderDetail'];
                return view('pages.checkout.success', compact('orderDetail'));
            }
            return redirect()->back()->with('error', $detail['message'] ?? 'Không lấy được thông tin đơn hàng.');
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function orderSuccess($orderId)
    {
        $result = $this->checkoutService->getOrderDetails($orderId);
        
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
