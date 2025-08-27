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

        /** @var AuthServiceInterfacef $user */
        $user = app(AuthServiceInterface::class)->getInfoAuth();

        if (is_array($user) && array_key_exists('membershipPlans', $user)) {
            $plans = collect($user['membershipPlans']);
        } elseif (is_object($user) && isset($user->membershipPlans)) {
            $plans = $user->membershipPlans instanceof \Illuminate\Support\Collection
                ? $user->membershipPlans
                : collect($user->membershipPlans);
        } else {
            $plans = collect();
        }
        $userId = $user->id;
        /** @var ProductServiceInterface $products */
        $productsCount = app(ProductServiceInterface::class)->getCountProductByCreatedByAndNearMonthly($userId);
        if ($plans->isEmpty()) {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Bạn cần mua gói thành viên để tạo sản phẩm. Vui lòng chọn gói để tiếp tục.')
                ->send();
            redirect()->to(BuyMembershipResource::getUrl());
        }

        $plansUsers = array_filter($user['membershipUsers']->all(), fn($item) => $item['status'] == CommonConstant::ACTIVE);
        if (!empty($plansUsers) && !empty($plansUsers[1]['membershipPlan']['config']) && $plansUsers[1]['membershipPlan']['config']['free_product_listing']) {
            $config = $plansUsers[1]['membershipPlan']['config'];

            if (!empty($config['free_product_listing'])) {
            } elseif ($productsCount >= ($config['max_products_per_month'] ?? 0)) {
                Notification::make()
                    ->title('Không đủ quyền')
                    ->warning()
                    ->body('Bạn đã đạt giới hạn tháng cần mua gói thành viên để tạo sản phẩm. Vui lòng chọn gói để tiếp tục.')
                    ->send();

                redirect()->to(BuyMembershipResource::getUrl());
            }
        } else {
            Notification::make()
                ->title('Không đủ quyền')
                ->warning()
                ->body('Bạn cần nâng cấp hoặc kích hoạt gói thành viên khác. Vui lòng chọn gói để tiếp tục.')
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
        // dd($data);
        return $productService->createProductWithSideEffects($data, auth()->id());
    }
}
