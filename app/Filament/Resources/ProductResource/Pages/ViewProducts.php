<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\Auctions\AuctionServiceInterface;
use Filament\Resources\Pages\ViewRecord;

class ViewProducts extends ViewRecord
{
    protected static string $resource = ProductResource::class;

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
}
