<?php

namespace App\Http\Controllers;

use App\Services\Auctions\AuctionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    protected $auctionService;
    protected $userId;
    public function __construct(AuctionServiceInterface $auctionService)
    {
        $this->auctionService = $auctionService;
        $this->middleware(function ($request, $next) {
			$this->userId = Auth::id();
			return $next($request);
		});
        
    }

    public function show($productId)
    {
        $result = $this->auctionService->getAuctionDetails($productId);
        
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return view('pages.auctions.show', $result['data']);
    }

    public function bid(Request $request, $productId)
    {
        $request->validate([
            'bid_price' => 'required|numeric|min:0',
        ]);
        $bidPrice = $request->bid_price;
        $result = $this->auctionService->placeBid($productId, $this->userId, $bidPrice);
        if ($result['success']) {
            $userBidData = [
                'auction_id' => $result['data']->auction_id,
                'user_id' => $this->userId,
                'bid_price' => $bidPrice,
                'bid_time' => now(),
                'created_at' => now()
            ];
            
            return redirect()->back()
                ->with('success', $result['message'])
                ->with('user_bid_data', $userBidData);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function placeBid(Request $request, $auctionId)
    {
        $request->validate([
            'bid_price' => 'required|numeric|min:0',
        ]);

        $bidPrice = $request->bid_price;

        $result = $this->auctionService->placeBid($auctionId, $this->userId, $bidPrice);
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    public function getHistory($auctionId)
    {
        $result = $this->auctionService->getAuctionHistory($auctionId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    public function getUserBidHistory($auctionId)
    {
        $result = $this->auctionService->getUserBidHistory($auctionId, $this->userId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    public function getActiveAuctions()
    {
        $result = $this->auctionService->getActiveAuctions();

        if ($result['success']) {
            return view('pages.auctions.index', ['auctions' => $result['data']]);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function getUserParticipatingAuctions()
    {
        $result = $this->auctionService->getUserParticipatingAuctions($this->userId);

        if ($result['success']) {
            return view('pages.auctions.user-participating', ['auctions' => $result['data']]);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
