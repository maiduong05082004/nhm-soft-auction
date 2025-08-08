<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
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
}
