<?php

namespace App\Filament\Resources\PageStaticResource\Pages;

use App\Enums\CommonConstant;
use App\Filament\Resources\PageStaticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditPageStatic extends EditRecord
{
    protected static string $resource = PageStaticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['meta_title']) && ! empty($data['title'])) {
            $data['meta_title'] = $data['title'];
        }

        if (($data['status'] ?? null) == CommonConstant::ACTIVE && empty($data['published_at'])) {
            $data['published_at'] = Carbon::now();
        }

        if (($data['status'] ?? null) === 'draft') {
            $data['published_at'] = null;
        }

        return $data;
    }
}
