<?php

namespace App\Filament\Resources\ConfigResource\Pages;

use App\Filament\Resources\ConfigResource;
use Filament\Resources\Pages\Page;

class Config extends Page
{
    protected static string $resource = ConfigResource::class;

    protected static string $view = 'filament.admin.resources.config.view';

    protected ?string $heading = 'Cấu hình hệ thống';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
