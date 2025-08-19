<?php

namespace App\Livewire\Filament;

use App\Filament\Resources\BuyMembershipResource;
use App\Services\Auth\AuthServiceInterface;
use Livewire\Component;

class ViewMembership extends Component
{
    private AuthServiceInterface $authService;
    /**
     * @var null
     */
    public $memberships;

    public function boot(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function mount()
    {
        $this->memberships = $this->authService->getMembershipInfo();
    }

    public function goToBuyMembership()
    {
        return redirect()->to(BuyMembershipResource::getUrl('buy'));
    }

    public function render()
    {
        return view('livewire.filament.view-membership');
    }
}
