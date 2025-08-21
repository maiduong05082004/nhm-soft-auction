<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\Product\ProductTypeSale;
use App\Filament\Resources\ProductResource;
use App\Services\Products\ProductServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Đăng bán sản phẩm";

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
