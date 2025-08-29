<?php

namespace App\Filament\Resources\PageStaticResource\Pages;

use App\Enums\CommonConstant;
use App\Filament\Resources\PageStaticResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreatePageStatic extends CreateRecord
{
    protected static string $resource = PageStaticResource::class;

    protected function getFormDefaults(): array
    {
        return [
            'template' => 'default',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['meta_title']) && ! empty($data['title'])) {
            $data['meta_title'] = $data['title'];
        }

        if (($data['status'] ?? null) == CommonConstant::ACTIVE) {
            $data['published_at'] = Carbon::now();
        } else {
            $data['published_at'] = null;
        }

        return $data;
    }
}
