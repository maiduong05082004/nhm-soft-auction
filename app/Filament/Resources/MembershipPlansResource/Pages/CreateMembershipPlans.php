<?php

namespace App\Filament\Resources\MembershipPlansResource\Pages;

use App\Filament\Resources\MembershipPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMembershipPlans extends CreateRecord
{
    protected static string $resource = MembershipPlansResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            url()->previous() => 'Gói thành viên',
            '' => 'Tạo gói thành viên',
        ];
    }
}
