<?php

namespace App\Http\Controllers;

use App\Enums\Membership\MembershipTransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\Transaction\TransactionServiceInterface;

class PayOSWebhookController extends Controller
{
    protected TransactionServiceInterface $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function handle(Request $request)
    {
        Log::info('PayOS Webhook:', $request->all());
        
        if($request->all()['data']['orderCode'] == 123){
            return response()->json(['message' => 'success']);
        }
        $data = $request->all();
        $orderCode = $data['data']['orderCode'] ?? null;

        if (!$orderCode) {
            Log::info('PayOS Webhook order code:', $orderCode);
            return response()->json(['message' => 'Missing orderCode'], 400);
        }
        if ($data["success"]) {

            $this->transactionService->confirmMembershipTransactionForwebhook($orderCode, MembershipTransactionStatus::ACTIVE->value);
            return response()->json(['message' => 'success']);
        } else {
            $this->transactionService->confirmMembershipTransactionForwebhook($orderCode, MembershipTransactionStatus::FAILED->value);
            return response()->json(['message' => 'failed']);
        }
    }
}
