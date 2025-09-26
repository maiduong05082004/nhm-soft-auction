<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Permission\RoleConstant;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            url()->previous() => 'Người dùng',
            '' => 'Tạo người dùng mới',
        ];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (!empty($data['new_password'])) {
            $data['password'] = $data['new_password'];
        }
        $data['email_verified_at'] = now();

        $user = static::getModel()::create($data);
        $user->assignRole($data['role']);
        return $user;
    }

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Tạo mới');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Hủy');
    }
}
