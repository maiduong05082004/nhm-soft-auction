<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            url()->previous() => 'Danh mục',
            '' => 'Tạo danh mục',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Category::where('name', $data['name'])->exists()) {
            Notification::make()
                ->title('Lỗi')
                ->body('Tên danh mục đã tồn tại.')
                ->danger()
                ->send();
            
            $this->halt();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
