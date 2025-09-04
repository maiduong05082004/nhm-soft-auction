<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\CommonConstant;
use App\Enums\Permission\RoleConstant;
use App\Enums\Product\ProductTypeSale;
use App\Filament\Resources\BuyMembershipResource;
use App\Filament\Resources\ProductResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Đăng bán sản phẩm";

    public function mount(): void
    {
        parent::mount();

        if (auth()->user()->hasRole(RoleConstant::ADMIN)) {
            return;
        }
        $user = app(AuthServiceInterface::class)->getInfoAuth();

        if (empty($user)) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Không tìm thấy thông tin người dùng. Vui lòng đăng nhập lại.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }

        $membershipPlans = collect();
        $membershipUsers = collect();
        $userId = null;

        if (is_array($user)) {
            $membershipPlans = collect($user['membershipPlans'] ?? []);
            $membershipUsers = collect($user['membershipUsers'] ?? []);
            $userId = $user['id'] ?? null;
        } elseif (is_object($user)) {
            $membershipPlans = $user->membershipPlans instanceof \Illuminate\Support\Collection
                ? $user->membershipPlans
                : collect($user->membershipPlans ?? []);
            $membershipUsers = $user->membershipUsers instanceof \Illuminate\Support\Collection
                ? $user->membershipUsers
                : collect($user->membershipUsers ?? []);
            $userId = $user->id ?? null;
        }

        if (empty($userId)) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Không xác định được ID người dùng.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }

        $productsCount = app(ProductServiceInterface::class)->getCountProductByCreatedByAndNearMonthly($userId);
        if ($membershipPlans->isEmpty()) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Bạn cần mua gói thành viên để tạo sản phẩm. Vui lòng chọn gói để tiếp tục.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }

        $planActive = $membershipUsers->first(function ($item) {
            $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
            return $status == CommonConstant::ACTIVE;
        });

        if (empty($planActive)) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Bạn cần nâng cấp hoặc kích hoạt gói thành viên khác. Vui lòng chọn gói để tiếp tục.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }

        $config = null;
        if (is_array($planActive)) {
            $config = $planActive['membershipPlan']['config'] ?? null;
        } elseif (is_object($planActive)) {
            $config = $planActive->membershipPlan->config ?? null;
        }

        if (empty($config) || !is_array($config) && !is_object($config)) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Cấu hình gói không hợp lệ. Vui lòng liên hệ quản trị.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }

        $cfg = is_array($config) ? $config : (array) $config;

        $freeListing = $cfg['free_product_listing'] ?? false;
        $maxPerMonth = array_key_exists('max_products_per_month', $cfg) ? $cfg['max_products_per_month'] : null;

        if ($freeListing) {
            return;
        }

        if (is_null($maxPerMonth) || $maxPerMonth == 0 ) {
            return;
        }

        if ( ($maxPerMonth > 0 && $productsCount >= $maxPerMonth)) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Bạn đã đạt giới hạn tháng cần mua gói thành viên để tạo sản phẩm. Vui lòng chọn gói để tiếp tục.')
                ->send();

            redirect()->to(BuyMembershipResource::getUrl());
        }
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $typeSale = is_object($data['type_sale']) && method_exists($data['type_sale'], 'value')
            ? $data['type_sale']->value
            : (int) $data['type_sale'];
        if ($typeSale === ProductTypeSale::SALE->value) {
            $data['min_bid_amount'] = 0;
            $data['max_bid_amount'] = 0;
            $data['start_time'] = null;
            $data['end_time'] = null;
        } else if ($typeSale === ProductTypeSale::AUCTION->value) {
            $data['price'] = $data['max_bid_amount'] ?? 0;
        }
        $data['created_by'] = auth()->user()->id;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $typeSale = is_object($data['type_sale']) && method_exists($data['type_sale'], 'value')
            ? $data['type_sale']->value
            : (int) $data['type_sale'];

        if ($typeSale === ProductTypeSale::SALE->value) {
            $data['min_bid_amount'] = 0;
            $data['max_bid_amount'] = 0;
            $data['start_time'] = null;
            $data['end_time'] = null;
        } else if ($typeSale === ProductTypeSale::AUCTION->value) {
            $data['price'] = $data['max_bid_amount'] ?? 0;
        }
        $data['created_by'] = auth()->user()->id;
        return $data;
    }


    protected function handleRecordCreation(array $data): Model
    {
        /** @var ProductServiceInterface $productService */
        $productService = app(ProductServiceInterface::class);
        $data['images'] = $data['images'] ?? [];
        return $productService->createProductWithSideEffects($data, auth()->id());
    }
}
