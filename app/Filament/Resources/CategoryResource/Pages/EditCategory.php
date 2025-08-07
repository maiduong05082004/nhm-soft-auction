<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận xóa vĩnh viễn')
                ->modalDescription('Bạn có chắc chắn muốn xóa danh mục này vĩnh viễn? Thao tác này không thể được hoàn tác.')
                ->action(function (Category $record) {
                    if ($record->children()->exists()) {
                        Notification::make()
                            ->title('Lỗi')
                            ->body('Không thể xóa danh mục vì nó có danh mục con.')
                            ->danger()
                            ->send();
                        return;
                    }

                    if ($record->products()->exists()) {
                        Notification::make()
                            ->title('Lỗi')
                            ->body('Không thể xóa danh mục vì nó có sản phẩm liên quan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record->forceDelete();
                    Notification::make()
                        ->title('Thành công')
                        ->body('Danh mục đã được xóa vĩnh viễn thành công!')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Category::where('name', $data['name'])->where('id', '!=', $this->record->id)->exists()) {
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
