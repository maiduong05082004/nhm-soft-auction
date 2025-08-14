<?php

namespace App\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\RegistrationResponse;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class OverrideRegistrationResponse  extends RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        // Thêm thông báo vào session
        Notification::make()
            ->title('Tạo tài khoản thành công')
            ->body('Vui lòng kiểm tra email của bạn để xác minh. Nếu bạn không nhận được email, vui lòng kiểm tra thư mục spam của mình.')
            ->success()
            ->send();
        // Redirect tới trang login
        return redirect()->intended(Filament::getUrl());

    }
}
