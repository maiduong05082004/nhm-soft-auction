<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthServiceInterface;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class AuthController extends Controller
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function verify($id, $hash)
    {
        $result = $this->authService->verifyEmailUser($id, $hash);
        if ($result) {
            Notification::make()
                ->title('Xác thực tài khoản thành công')
                ->body("Hãy đăng nhập bằng tài khoản bạn đã xác thực")
                ->success()
                ->send();
            // Redirect tới trang login
        } else {
            Notification::make()
                ->title('Xác thực tài khoản thất bại')
                ->body("Vui lòng liên hệ với quản trị để khắc phục sự cố")
                ->success()
                ->send();
        }
        return redirect()->intended(Filament::getUrl());
    }
}
