<?php

namespace App\Livewire\Filament;

use App\Filament\Resources\BuyMembershipResource;
use App\Models\MembershipUser;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Membership\MembershipServiceInterface;
use Filament\Notifications\Notification;
use Livewire\Component;

class ViewMembership extends Component
{
    private AuthServiceInterface $authService;
    private MembershipServiceInterface $membershipsService;

    /**
     * State
     */

    public $memberships;

    public function boot(AuthServiceInterface $authService, MembershipServiceInterface $membershipsService)
    {
        $this->authService = $authService;
        $this->membershipsService = $membershipsService;
    }

    public function mount()
    {
        $this->memberships = $this->authService->getMembershipInfo();
    }

    public function shouldShowButtonActive(MembershipUser $item): bool
    {
        return $this->membershipsService->validateActiveMembership($item);
    }

    public function activeMembership($id)
    {
        $membershipUser = $this->membershipsService->getById('membershipUser', $id);
        if (!$membershipUser) {
            Notification::make()
                ->title("Không tìm thấy thông tin gói thành viên này")
                ->danger()
                ->send();
            return;
        }
        $valid = $this->membershipsService->validateActiveMembership($membershipUser);
        if (!$valid){
            Notification::make()
                ->title("Gói thành viên này không hợp lệ hoặc đã hết hạn")
                ->danger()
                ->send();
            return;
        }
        $result = $this->membershipsService->reActivateMembershipForUser($membershipUser);
        if ($result) {
            Notification::make()
                ->title("Kích hoạt gói thành viên thành công")
                ->success()
                ->send();
            $this->mount();
        } else {
            Notification::make()
                ->title("Kích hoạt gói thành viên thất bại")
                ->danger()
                ->send();
        }
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
