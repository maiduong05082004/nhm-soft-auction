<?php

namespace App\Livewire\Filament;

use App\Enums\Config\ConfigName;
use App\Enums\PayTypes;
use App\Filament\Resources\BuyMembershipResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Config\ConfigServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use App\Utils\HelperFunc;
use Filament\Notifications\Notification;
use Livewire\Component;

class BuyMembership extends Component
{
    private AuthServiceInterface $authService;

    private MembershipServiceInterface $membershipService;

    private ConfigServiceInterface $configService;

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
        $this->nextStepBuy = true;
        $membership = $this->membershipService->getMembershipPlanById($id);
        if ($membership) {
            $config = $this->configService->getConfigByKeys([
                ConfigName::ADMIN_ACCOUNT_BANK_BIN,
                ConfigName::ADMIN_ACCOUNT_BANK_ACCOUNT,
                ConfigName::ADMIN_ACCOUNT_BANK_NAME,
                ConfigName::PRICE_ONE_COIN,
            ]);
            $totalPrice = $membership->price * $config[ConfigName::PRICE_ONE_COIN->value];
            $descBank = "MEMBERSHIP" . HelperFunc::getTimestampAsId();
            $urlBankQrcode = HelperFunc::generateQRCodeBanking(
                binBank: $config[ConfigName::ADMIN_ACCOUNT_BANK_BIN->value],
                bankNumber: $config[ConfigName::ADMIN_ACCOUNT_BANK_ACCOUNT->value],
                bankName: $config[ConfigName::ADMIN_ACCOUNT_BANK_NAME->value],
                amount: $totalPrice,
                addInfo: $descBank,
            );
            $this->membership = $membership;
            $this->dataTransfer = [
                'urlBankQrcode' => $urlBankQrcode,
                'totalPrice' => $totalPrice,
                'descBank' => $descBank,
                'points' => $membership->price
            ];
            return;
        }
        Notification::make()
            ->title('Lỗi')
            ->body('Có lỗi xảy ra, vui lòng thử lại sau.')
            ->danger()
            ->send();
        $this->nextStepBuy = false;
        $this->membership = null;
    }

    public function submit($payType)
    {   
        $result = $this->membershipService->createMembershipForUser(
            userId: auth()->id(),
            membershipPlan: $this->membership,
            dataTransfer: $this->dataTransfer,
            payType: $payType
        );
        if ($result) {
            Notification::make()
                ->title('Thành công')
                ->body('Thanh toán thành công, vui lòng chờ duyệt.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Lỗi')
                ->body('Có lỗi xảy ra, vui lòng thử lại sau.')
                ->danger()
                ->send();
        }
        return redirect()->to(BuyMembershipResource::getUrl());
    }

    public function render()
    {
        return view('livewire.filament.buy-membership');
    }
}
