<?php

namespace App\Livewire\Filament;

use App\Enums\Config\ConfigName;
use App\Filament\Resources\PointPackageResource;
use App\Services\Config\ConfigServiceInterface;
use App\Services\PointPackages\PointPackageServiceInterface;
use App\Utils\HelperFunc;
use Filament\Notifications\Notification;
use Livewire\Component;

class BuyPointPackage extends Component
{

    private PointPackageServiceInterface $pointPackageService;
    private ConfigServiceInterface $configService;
    /**
     * State
     */
    public $list;

    public $nextStepBuy = false;

    public $pointPackage = null;

    public $dataTransfer = [];

    public function boot(PointPackageServiceInterface $pointPackageService, ConfigServiceInterface $configService)
    {
        $this->pointPackageService = $pointPackageService;
        $this->configService = $configService;
    }

    public function selectPackage($packageId)
    {
        $this->nextStepBuy = true;
        $config = $this->configService->getConfigByKeys([
            ConfigName::ADMIN_ACCOUNT_BANK_BIN,
            ConfigName::ADMIN_ACCOUNT_BANK_ACCOUNT,
            ConfigName::ADMIN_ACCOUNT_BANK_NAME,
            ConfigName::PRICE_ONE_COIN,
        ]);

        $pointPackage = $this->pointPackageService->getPointPackageById($packageId);

        if ($pointPackage) {
            $totalPrice = $config[ConfigName::PRICE_ONE_COIN->value] * $pointPackage['points'] * ((100 - $pointPackage['discount']) / 100);
            $descBank = "POINTPACKAGE" . HelperFunc::getTimestampAsId();
            $urlBankQrcode = HelperFunc::generateQRCodeBanking(
                binBank: $config[ConfigName::ADMIN_ACCOUNT_BANK_BIN->value],
                bankNumber: $config[ConfigName::ADMIN_ACCOUNT_BANK_ACCOUNT->value],
                bankName: $config[ConfigName::ADMIN_ACCOUNT_BANK_NAME->value],
                amount: $totalPrice,
                addInfo: $descBank,
            );
            $this->pointPackage = $pointPackage;
            $this->dataTransfer = [
                'urlBankQrcode' => $urlBankQrcode,
                'totalPrice' => $totalPrice,
                'descBank' => $descBank,
                'points' => $pointPackage['points']
            ];
            return;
        }

        Notification::make()
            ->title('Lỗi')
            ->body('Có lỗi xảy ra, vui lòng thử lại sau.')
            ->danger()
            ->send();
        $this->nextStepBuy = false;
        $this->pointPackage = null;
    }

    public function mount()
    {
        $this->list = $this->pointPackageService->getAllPointPackage();
    }

    public function confirmPaymentSuccess()
    {
        $result = $this->pointPackageService->createTransactionPayment(
            dataTransfer: $this->dataTransfer,
            userId: auth()->id(),
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
        return redirect()->to(PointPackageResource::getNavigationUrl());
    }

    public function render()
    {
        return view('livewire.filament.point-package.buy-point-package');
    }
}
