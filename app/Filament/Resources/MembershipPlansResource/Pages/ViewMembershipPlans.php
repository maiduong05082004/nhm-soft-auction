<?php

namespace App\Filament\Resources\MembershipPlansResource\Pages;

use App\Filament\Resources\MembershipPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMembershipPlans extends ViewRecord
{
    protected static string $resource = MembershipPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
