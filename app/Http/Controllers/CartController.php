<?php

namespace App\Http\Controllers;

use App\Enums\Permission\RoleConstant;
use App\Services\Cart\CartServiceInterface;
use App\Services\Checkout\CheckoutServiceInterface;
use App\Repositories\Products\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
	protected $cartService;
	protected $checkoutService;
	protected $productRepo;
	protected $userId;

	public function __construct(CartServiceInterface $cartService, CheckoutServiceInterface $checkoutService, ProductRepository $productRepo)
	{
		$this->cartService = $cartService;
		$this->checkoutService = $checkoutService;
		$this->productRepo = $productRepo;
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
		$auctionCheckoutData = session('auction_checkout_data');

		if ($auctionCheckoutData) {
			$product = $this->productRepo->find($auctionCheckoutData['product_id']);

			$cartItems = collect([
				(object) [
					'product_id' => $auctionCheckoutData['product_id'],
					'quantity' => $auctionCheckoutData['quantity'],
					'price' => $auctionCheckoutData['amount'],
					'total' => $auctionCheckoutData['amount'],
					'product' => (object) [
						'name' => $product->name ?? 'Sản phẩm trả giá',
						'price' => $auctionCheckoutData['amount'],
						'images' => $product->images ?? collect(),
					]
				]
			]);

			$user = auth()->user();
			$auctionCheckoutData['name'] = $user->name ?? '';
			$auctionCheckoutData['phone'] = $user->phone ?? '';
			$auctionCheckoutData['product_slug'] = $product->slug ?? '';

			$total = $auctionCheckoutData['amount'];

			$hasCreditCard = false;
			foreach ($cartItems as $item) {
				$product = $this->productRepo->find($item->product_id);
				if ($product->owner->creditCard || $product->owner->hasRole(RoleConstant::ADMIN)) {
					$hasCreditCard = true;
					break;
				}
			}

			$discountInfo = $this->checkoutService->getCheckoutDiscountInfo($this->userId, $total);

			return view('pages.checkout.checkout', compact('cartItems', 'total', 'hasCreditCard', 'auctionCheckoutData', 'discountInfo'));
		}

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
			$hasCreditCard = false;
			foreach ($cartItems as $item) {
				if ($item->product->owner->creditCard || $item->product->owner->hasRole(RoleConstant::ADMIN)) {
					$hasCreditCard = true;
					break;
				}
			}

			$discountInfo = $this->checkoutService->getCheckoutDiscountInfo($this->userId, $total);

			return view('pages.checkout.checkout', compact('cartItems', 'total', 'hasCreditCard', 'discountInfo'));
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
			'selected' => 'nullable|string',
			'auction_id' => 'nullable|integer',
			'note' => 'nullable|string|max:1000'
		]);

		if ($request->filled('auction_id')) {
			$checkoutData = [
				'auction_id' => $request->auction_id,
				'name' => $request->name,
				'email' => $request->email,
				'phone' => $request->phone,
				'address' => $request->address,
				'payment_method' => $request->payment_method,
				'note' => $request->note ?? '',
			];

			$result = $this->checkoutService->processAuctionCheckout($this->userId, $checkoutData);

			if ($result['success']) {
				$orderId = $result['data']['order_detail_id'];
				$paymentStatus = $result['data']['payment_status'];

				if ($paymentStatus === 'success') {
					return redirect()->route('order.success', ['order' => $orderId]);
				} else {
					return redirect()->route('payment.qr', ['order' => $orderId]);
				}
			} else {
				return redirect()->back()->with('error', $result['message']);
			}
		}

		$checkoutData = [
			'name' => $request->name,
			'phone' => $request->phone,
			'address' => $request->address,
			'email' => $request->email,
			'payment_method' => $request->payment_method,
			'note' => $request->note ?? '',
			'selected' => $request->selected
		];

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
			$payment = collect($result['data']['payment']);
			foreach ($payment as $pay) {
				if ($pay->orders->first()->product->owner->hasRole(RoleConstant::ADMIN)) {
					$pay->vietqr = $this->checkoutService->buildVietQrUrl($pay, \App\Utils\HelperFunc::getAdminCreditCard(), $orderDetail);
				} else if ($pay->orders->first()->product->owner->creditCard) {
					$pay->vietqr = $this->checkoutService->buildVietQrUrl($pay, $pay->orders->first()->product->owner->creditCard, $orderDetail);
				}
			}
			return view('pages.checkout.payment', compact('orderDetail', 'payment'));
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

	public function auctionPayNow(Request $request)
	{
		$request->validate([
			'auction_id' => 'required|integer'
		]);
		$auctionId = (int) $request->auction_id;
		$init = $this->checkoutService->processAuctionWinnerPayment($this->userId, $auctionId);
		if ($init['success']) {
			$checkoutData = $init['data']['checkout_data'];
			return redirect()->route('cart.checkout')
				->with('auction_checkout_data', $checkoutData);
		}
		return redirect()->back()->with('error', $init['message'] ?? 'Không khởi tạo được thanh toán.');
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
