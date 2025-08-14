<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Users\UserRepository;
use App\Services\BaseService;

class AuthService extends BaseService implements AuthServiceInterface
{
    public function __construct(UserRepository $userRepo)
    {
        parent::__construct([
            'user' => $userRepo
        ]);
    }

    public function verifyEmailUser($id, $hash): bool
    {
        try {
            /**
             * @var $user User
             */
            $user = $this->getRepository('user')->find($id);
            if (!$user) return false;

            // Kiểm tra xem hash có hợp lệ không
            if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
                return false;
            }

            // Xác thực email người dùng
            if ($user->hasVerifiedEmail()) {
                return false;
            }

            $user->markEmailAsVerified();

            return true;
        }catch (\Exception $exception){
            return false;
        }
    }
}
