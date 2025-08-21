<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
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
