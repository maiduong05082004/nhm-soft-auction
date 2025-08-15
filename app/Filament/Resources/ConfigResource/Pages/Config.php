<?php

namespace App\Filament\Resources\ConfigResource\Pages;

use App\Filament\Resources\ConfigResource;
use App\Services\Config\ConfigServiceInterface;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\Validate;


class Config extends Page
{
    protected static string $resource = ConfigResource::class;

    protected static string $view = 'filament.admin.resources.config.view';

    protected ?string $heading = 'Cấu hình hệ thống';
    private ConfigServiceInterface $service;

    public function mount(ConfigServiceInterface $service): void
    {
        $this->service = $service;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function updateConfig()
    {
        dd($this->config_value);
    }
}
