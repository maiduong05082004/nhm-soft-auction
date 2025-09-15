<?php

namespace App\Livewire\Filament;

use App\Enums\CommonConstant;
use App\Enums\Config\ConfigName;
use App\Enums\Membership\MembershipTransactionStatus;
use App\Filament\Resources\BuyMembershipResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Config\ConfigServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use App\Utils\HelperFunc;
use Doctrine\DBAL\Exception\ConnectionException;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UpgradeMembership extends Component
{
    private AuthServiceInterface $authService;

    private MembershipServiceInterface $membershipService;

    public $status = MembershipTransactionStatus::WAITING->value;


    public $paymentSuccess = false;

    private ConfigServiceInterface $configService;

    /**
     * State
     */
    public $list;


    public $nextStepUpgrade = false;

    public $currentplan = null;

    public $dataTransfer = [];

    public $membershipPay = null;

    public $user = null;

    public function boot(MembershipServiceInterface $membershipService, ConfigServiceInterface $configService, AuthServiceInterface $authService)
    {
        $this->membershipService = $membershipService;
        $this->configService = $configService;
        $this->authService = $authService;
    }

    public function mount()
    {
        $this->user = $this->authService->getInfoAuth();
        $this->currentplan = $this->user['activeMemberships']->firstWhere('status', CommonConstant::ACTIVE);
        if ($this->currentplan) {
            $this->list = $this->membershipService->getAllMembershipPlan()->filter(fn($item) => $item->id != $this->currentplan->id && $item->price > $this->currentplan->price || $item->duration > $this->currentplan->duration);
        } else {
            $this->currentplan = $this->user->membershipPlans()->wherePivot('status', CommonConstant::INACTIVE)->first();
            $this->list = $this->membershipService->getAllMembershipPlan();
        }
    }

    public function onNextStep($id)
    {
        $this->nextStepUpgrade = true;
        $membership = $this->membershipService->getMembershipPlanById($id);
        if ($membership) {
            $config = $this->configService->getConfigByKeys([
                ConfigName::API_KEY,
                ConfigName::CLIENT_ID_APP,
                ConfigName::CHECKSUM_KEY,
                ConfigName::PRICE_ONE_COIN,
            ]);
            $totalPrice = $membership->price * $config[ConfigName::PRICE_ONE_COIN->value];
            $descBank = "MBS" . HelperFunc::getTimestampAsId();


            try {
                $orderCode = (int) (microtime(true) * 1000);

                $amount = (int) $totalPrice;
                $returnUrl = route('home');
                $cancelUrl = route('home');
                $desc = $descBank;
                $expiredAtForApi = now()->addMinutes(5)->timestamp;

                $payload = [
                    'amount' => $amount,
                    'cancelUrl' => $cancelUrl,
                    'description' => $desc,
                    'orderCode' => $orderCode,
                    'returnUrl' => $returnUrl,
                ];

                $signature = HelperFunc::generateSignature($payload, $config[ConfigName::CHECKSUM_KEY->value]);

                $response = Http::withHeaders([
                    'X-Client-Id' => $config[ConfigName::CLIENT_ID_APP->value],
                    'X-Api-Key'   => $config[ConfigName::API_KEY->value],
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(15)
                    ->post('https://api-merchant.payos.vn/v2/payment-requests', array_merge($payload, [
                        'expiredAt' => $expiredAtForApi,
                        'signature' => $signature
                    ]))
                    ->json();
                if (! $response || ($response['desc'] ?? '') !== "success") {
                    Log::error('PayOS create payment error', [
                        'payload' => $payload,
                        'response' => $response,
                    ]);

                    Notification::make()
                        ->title('Lỗi')
                        ->body('Thanh toán không thành công: ' . ($response['desc'] ?? 'Không rõ nguyên nhân'))
                        ->danger()
                        ->send();

                    return;
                }

                $qrCodeUrl = HelperFunc::generateQRCodeBanking(
                    $response['data']['bin'],
                    $response['data']['accountNumber'],
                    $response['data']['accountName'],
                    (int) $totalPrice,
                    $descBank
                );

                $this->membershipPay = $membership;

                $this->dataTransfer = [
                    'urlBankQrcode' => $qrCodeUrl,
                    'checkoutUrl'   => $response['data']['checkoutUrl'],
                    'totalPrice'    => $totalPrice,
                    'descBank'      => $descBank,
                    'points'        => $membership->price,
                    'orderCode'     => $orderCode,
                    'expiredAt'     => $expiredAtForApi,
                ];

                $this->membershipService->createMembershipForUser(
                    userId: auth()->id(),
                    membershipPlan: $this->membershipPay,
                    dataTransfer: $this->dataTransfer,
                );
            } catch (ConnectionException $e) {
                Log::error("PayOS timeout/connection error", ['msg' => $e->getMessage()]);
                Notification::make()
                    ->title('Lỗi')
                    ->body('Không thể kết nối tới PayOS, vui lòng thử lại sau.')
                    ->danger()
                    ->send();
            } catch (\Throwable $e) {
                Log::error("Unexpected error", ['msg' => $e->getMessage()]);
                Notification::make()
                    ->title('Lỗi')
                    ->body('Có lỗi xảy ra khi tạo QR code, vui lòng thử lại sau.')
                    ->danger()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Lỗi')
                ->body('Có lỗi xảy ra, vui lòng thử lại sau.')
                ->danger()
                ->send();
            $this->nextStepUpgrade = false;
            $this->membershipPay = null;
        }
    }

    public function refreshOrder()
    {
        if (!empty($this->dataTransfer['expiredAt']) && $this->dataTransfer['expiredAt'] < now()->timestamp) {
            $this->status = MembershipTransactionStatus::FAILED->value;

            Notification::make()
                ->title('Hết hạn')
                ->body('Thanh toán đã hết hạn, vui lòng tạo đơn mới.')
                ->danger()
                ->send();

            return redirect()->to(\App\Filament\Resources\BuyMembershipResource::getUrl());
        }

        $result = $this->membershipService->refreshMemberShipTransaction($this->user->id, $this->dataTransfer);
        $this->status = $result;

        if ($result == MembershipTransactionStatus::WAITING->value) {

        } else if ($result == MembershipTransactionStatus::ACTIVE->value) {
            $this->paymentSuccess = true;

            $this->dispatch('payment-success');
        } else {
            Notification::make()
                ->title('Thất bại')
                ->body('Có lỗi xảy ra, vui lòng thử lại sau.')
                ->danger()
                ->send();
            return redirect()->to(BuyMembershipResource::getUrl());
        }
    }


    public function redirectAfterSuccess()
    {
        return redirect()->to(BuyMembershipResource::getUrl());
    }

    public function render()
    {
        return view('livewire.filament.upgrade-membership');
    }
}
