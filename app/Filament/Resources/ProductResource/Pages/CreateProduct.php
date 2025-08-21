<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\Product\ProductTypeSale;
use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use App\Models\Auction;
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


    protected function afterCreate(): void
    {
        $product = $this->record;
        $images = $this->data['images'] ?? [];
        $nu = 1;
        foreach ($images as $imagePath) {

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $imagePath,
                'status' => 'active',
                'position' => $nu
            ]);
            $nu++;
        }

        // Auto create auction record if type_sale is AUCTION
        $typeSale = is_object($this->data['type_sale']) && method_exists($this->data['type_sale'], 'value')
            ? $this->data['type_sale']->value
            : (int) $this->data['type_sale'];
        if ($typeSale === ProductTypeSale::AUCTION->value) {
            Auction::create([
                'product_id' => $product->id,
                'start_price' => $this->data['min_bid_amount'] ?? 0,
                'step_price' => $this->data['step_price'] ?? 10000,
                'start_time' => $this->data['start_time'] ?? now(),
                'end_time' => $this->data['end_time'] ?? now()->addDays(7),
                'status' => 'active',
            ]);
        }
    }
}
