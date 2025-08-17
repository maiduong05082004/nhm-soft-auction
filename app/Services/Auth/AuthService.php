<?php

namespace App\Services\Auth;

use App\Enums\ImageStoragePath;
use App\Models\User;
use App\Repositories\Users\UserRepository;
use App\Services\BaseService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function getInfoAuth()
    {
        /**
         * @var User $user
         */
        return $this->getRepository('user')->query()->with('creditCards')->find(auth()->id());
    }

    public function updateAuthUser(array $data)
    {
        $user = $this->getInfoAuth();
        try {
            DB::beginTransaction();

            // Cập nhật thông tin cơ bản
            $user->name = $data['name'];
            $user->phone = $data['phone'];
            $user->address = $data['address'];
            $user->introduce = $data['introduce'];
            // Cập nhật mật khẩu nếu có
            if (!empty($data['new_password'])) {
                $user->password = $data['new_password'];
            }
            // Cập nhật avatar nếu có
            if (!empty($data['profile_photo_url'])) {
                // Xóa avatar cũ nếu có
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                // Lưu file mới
                $user->profile_photo_path = $data['profile_photo_url']->store(ImageStoragePath::AVATAR_USER->value, 'public');
            }
            // Cập  người dùng
            $user->save();

            // Lấy thẻ ngân hàng của người dùng
            $creditCard = $user->creditCards()->first();

            // Cập nhật thông tin thẻ ngân hàng
            $bin = $data['bin_bank'] ?? null;
            $cardNumber = $data['card_number'] ?? null;
            $cardHolder = $data['card_holder_name'] ?? null;

            // Nếu có thông tin thẻ ngân hàng, cập nhật hoặc tạo mới
            if ($bin && $cardNumber && $cardHolder) {
                if ($creditCard) {
                    // Cập nhật thẻ ngân hàng nếu đã có
                    $creditCard->update([
                        'bin_bank' => $bin,
                        'card_number' => $cardNumber,
                        'card_holder_name' => $cardHolder,
                    ]);
                } else {
                    // Tạo mới thẻ ngân hàng nếu chưa có
                    $user->creditCards()->create([
                        'bin_bank' => $bin,
                        'card_number' => $cardNumber,
                        'name' => $cardHolder,
                        'user_id' => $user->id,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
