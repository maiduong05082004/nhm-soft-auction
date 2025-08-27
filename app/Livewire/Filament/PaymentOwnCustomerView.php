<?php

namespace App\Livewire\Filament;

use App\Services\Auth\AuthServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use App\Services\Orders\OrderServiceInterface;
use App\Services\PointPackages\PointPackageServiceInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Component;

class PaymentOwnCustomerView extends Component
{

    private AuthServiceInterface $authService;

    public ?Authenticatable $auth;

    public $sumTransaction;

    public function boot(
        AuthServiceInterface $authService,
        PointPackageServiceInterface $pointPackageService,
        MembershipServiceInterface $membershipService,
        OrderServiceInterface $orderService
    ) {
        $this->authService = $authService;
    }
    public function mount(): void
    {
        $this->auth = $this->authService->getInfoAuth();
        $this->sumTransaction = $this->authService->getSumTransaction();
    }

    public function render()
    {
        return view('livewire.filament.payment-own-customer-view');
    }
}
