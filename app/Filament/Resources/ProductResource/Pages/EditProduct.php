<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\Auctions\AuctionServiceInterface;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $typeSale = is_string($data['type_sale']) ? $data['type_sale'] : (string) $data['type_sale'];
        if ($typeSale === 'sale' || $typeSale === '1') {
            $data['min_bid_amount'] = 0;
            $data['max_bid_amount'] = 0;
            $data['start_time'] = null;
            $data['end_time'] = null;
        } else if ($typeSale === 'auction' || $typeSale === '2') {
            $data['price'] = $data['max_bid_amount'] ?? 0;
        }
        if (!empty($data['step_price'])) {
            $auctionService = app(AuctionServiceInterface::class);
            $auctionService->updateStepPriceByProductId((int) $this->record->id, (float) $data['step_price']);
        }
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var AuctionServiceInterface $auctionService */
        $auctionService = app(AuctionServiceInterface::class);
        $res = $auctionService->getAuctionDetails($this->record->id);
        if (!empty($res['success']) && !empty($res['data']['auction'])) {
            $data['step_price'] = $res['data']['auction']->step_price;
        }
        return $data;
    }



    public function getBreadcrumbs(): array
    {
        return [
            'products' => 'Sản phẩm',
            'products' => 'Chỉnh sửa',
        ];
    }
}
