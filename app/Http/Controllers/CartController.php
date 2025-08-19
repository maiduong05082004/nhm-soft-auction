<?php

namespace App\Http\Controllers;

use App\Services\Cart\CartServiceInterface;
use App\Services\Checkout\CheckoutServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CartController extends Controller
{
	protected $cartService;
	protected $checkoutService;
	protected $userId;

	public function __construct(CartServiceInterface $cartService, CheckoutServiceInterface $checkoutService)
	{
		$this->cartService = $cartService;
		$this->checkoutService = $checkoutService;
		$this->middleware(function ($request, $next) {
			$this->userId = Auth::id();
			return $next($request);
		});
	}

	public function addToCart(Request $request, $productId)
	{
		$quantity = $request->input('quantity', 1);

		$result = $this->cartService->addToCart($this->userId, $productId, $quantity);
		
		if ($result['success']) {
			return redirect()->route('cart.index')->with('success', $result['message']);
		} else {
			return redirect()->back()->with('error', $result['message']);
		}
	}

	public function index()
	{
		$result = $this->cartService->getUserCart($this->userId);

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
		$result = $this->cartService->getUserCart($this->userId);

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

			$hasCreditCard = $this->checkoutService->hasCreditCardConfig();
			return view('pages.checkout.checkout', compact('cartItems', 'total', 'hasCreditCard'));
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
		$checkoutData = [
			'name' => $request->name,
			'phone' => $request->phone,
			'address' => $request->address,
			'email' => $request->email,
			'payment_method' => $request->payment_method,
			'note' => $request->note ?? '',
			'selected' => $request->selected
		];

		if ($checkoutData['payment_method'] == '1' && !$this->checkoutService->hasCreditCardConfig()) {
			return redirect()->back()->with('error', 'Chưa cấu hình thẻ ngân hàng để tạo QR.');
		}

		$result = $this->checkoutService->processCheckout($this->userId, $checkoutData);
		
		if ($result['success']) {
			$paymentMethod = $checkoutData['payment_method'];
			$orderId = $result['data']['order_detail_id'];
			
			if ($paymentMethod == '1') {
				return redirect()->route('payment.qr', ['order' => $orderId]);
			} else {
				return $this->orderSuccess($orderId);
			}
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
			if (!$this->checkoutService->hasCreditCardConfig()) {
				return redirect()->route('cart.index')->with('error', 'Thiếu cấu hình thẻ ngân hàng để tạo QR.');
			}

			$vietqrUrl = $this->checkoutService->buildVietQrUrl($orderDetail, $payment);

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

		$result = $this->cartService->updateQuantity($this->userId, $request->product_id, $request->quantity);
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

		$result = $this->cartService->removeItem($this->userId, $request->product_id);
		if ($result['success']) {
			return response()->json($result);
		} else {
			return response()->json($result, 400);
		}
	}

	public function clearCart()
	{
		$result = $this->cartService->clearCart($this->userId);

		if ($result['success']) {
			return response()->json($result);
		} else {
			return response()->json($result, 400);
		}
	}
}
