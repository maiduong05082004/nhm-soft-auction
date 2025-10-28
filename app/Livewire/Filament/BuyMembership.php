<?php

namespace App\Livewire\Filament;

use App\Enums\Config\ConfigName;
use App\Enums\Membership\MembershipTransactionStatus;
use Illuminate\Http\Client\ConnectionException;
use App\Filament\Resources\BuyMembershipResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Config\ConfigServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use App\Utils\HelperFunc;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BuyMembership extends Component
{
    private AuthServiceInterface $authService;

    private MembershipServiceInterface $membershipService;

    private ConfigServiceInterface $configService;

    public $status = MembershipTransactionStatus::WAITING->value;


    public $paymentSuccess = false;

    /**
     * State
     */
    public $list;

    public $nextStepBuy = false;

    public $membership = null;

    public $dataTransfer = [];

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
        $this->list = $this->membershipService->getAllMembershipPlan();
    }

    public function onNextStep($id)
    {
        $membership = $this->membershipService->getMembershipPlanById($id);
        if ($membership) {

            if ($membership->is_testing) {
                $planTesting = $this->user->membershipPlans->firstWhere('is_testing', true);

                if ($planTesting) {
                    Notification::make()
                        ->title('Lỗi')
                        ->body('Bạn không thể tiếp tục kịch hoạt gói dùng thử')
                        ->danger()
                        ->send();

                    return;
                }

                $result = $this->membershipService->createMembershipForUser(
                    userId: auth()->id(),
                    membershipPlan: $membership,
                    dataTransfer: $this->dataTransfer,
                );
                if ($result) {
                    Notification::make()
                        ->title('Thành công')
                        ->body('Đăng ký gói dùng thử thành công')
                        ->success()
                        ->send();
                        $this->dispatch('redirect-after-delay', url: '/admin/buy-memberships', delay: 1000);
                } else {
                    Notification::make()
                        ->title('Lỗi')
                        ->body('Không thể kịch hoạt gói dùng thử')
                        ->danger()
                        ->send();

                    return;
                }

                return;
            }

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
                $expiredAtForApi = now()->addMinutes(15)->timestamp;

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

                $this->membership = $membership;

                $this->dataTransfer = [
                    'urlBankQrcode' => $qrCodeUrl,
                    'checkoutUrl'   => $response['data']['checkoutUrl'],
                    'totalPrice'    => $totalPrice,
                    'descBank'      => $descBank,
                    'points'        => $membership->price,
                    'orderCode'     => $orderCode,
                    'expiredAt'     => $expiredAtForApi,
                ];

                $this->nextStepBuy = true;

                $this->membershipService->createMembershipForUser(
                    userId: auth()->id(),
                    membershipPlan: $this->membership,
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
            $this->nextStepBuy = false;
            $this->membership = null;
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
            // chưa thanh toán
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
        return view('livewire.filament.buy-membership');
    }
}
