<?php

namespace App\Services\Auth;

use App\Enums\ImageStoragePath;
use App\Enums\Transactions\TransactionPaymentType;
use App\Models\User;
use App\Repositories\TransactionPayment\TransactionPaymentRepositoryInterface;
use App\Repositories\TransactionPoint\TransactionPointRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthService extends BaseService implements AuthServiceInterface
{
    public function __construct(
        UserRepositoryInterface               $userRepo,
        TransactionPaymentRepositoryInterface $transactionPaymentRepo,
        TransactionPointRepositoryInterface   $transactionPointRepo
    )
    {
        parent::__construct([
            'user' => $userRepo,
            'transactionPayment' => $transactionPaymentRepo,
            'transactionPoint' => $transactionPointRepo,
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
            if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
                return false;
            }

            // Xác thực email người dùng
            if ($user->hasVerifiedEmail()) {
                return false;
            }

            $user->markEmailAsVerified();

            return true;
        } catch (\Exception $exception) {
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

    public function updateAuthUser(array $data): bool
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

    public function getSumTransaction()
    {

        $userId = $this->getInfoAuth()->id;

        $repoPayment = $this->getRepository('transactionPayment');
        $repoPoint = $this->getRepository('transactionPoint');

        // Lấy tổng số tiền nạp
        $sumRechange = $repoPayment->sumTransTypeByUserId(TransactionPaymentType::RECHANGE_POINT, $userId);

        // Lấy tổng số tiền mua sản phẩm
        $sumBuyProduct = $repoPayment->sumTransTypeByUserId(TransactionPaymentType::BUY_PRODUCT, $userId);

        // Lấy tổng số tiền mua sản phẩm đấu giá
        $sumBidProduct = $repoPayment->sumTransTypeByUserId(TransactionPaymentType::BID_PRODUCT, $userId);

        // Lấy tổng số point
        $sumPoint = $repoPoint->sumTransByUserId($userId);

        return [
            'sum_rechange' => $sumRechange,
            'sum_buy_product' => $sumBuyProduct,
            'sum_bid_product' => $sumBidProduct,
            'sum_point' => $sumPoint,
        ];
    }
}
