<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['type_sale'] === 'sale') {
            $data['min_bid_amount'] = null;
            $data['max_bid_amount'] = null;
            $data['start_time'] = null;
            $data['end_time'] = null;
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
    }

    public function getBreadcrumbs(): array
    {
        return [
            'products' => 'Sản phẩm',
            'products' => 'Tạo mới',
        ];
    }
}
