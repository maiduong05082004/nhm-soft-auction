<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Resources\Pages\Page;

class test extends Page
{
    protected static string $resource = TestResource::class;

    protected static string $view = 'filament.resources.test-resource.pages.test';
}
