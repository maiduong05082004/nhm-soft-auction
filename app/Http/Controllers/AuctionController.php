<?php

namespace App\Http\Controllers;

use App\Services\Auctions\AuctionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    protected $auctionService;

    public function __construct(AuctionServiceInterface $auctionService)
    {
        $this->auctionService = $auctionService;
    }

    public function show($productId)
    {
        $result = $this->auctionService->getAuctionDetails($productId);
        
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return view('pages.auctions.show', $result['data']);
    }

    public function placeBid(Request $request, $auctionId)
    {
        $request->validate([
            'bid_price' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id();
        $bidPrice = $request->bid_price;

        $result = $this->auctionService->placeBid($auctionId, $userId, $bidPrice);

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

    public function getActiveAuctions()
    {
        $result = $this->auctionService->getActiveAuctions();

        if ($result['success']) {
            return view('pages.auctions.index', ['auctions' => $result['data']]);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
