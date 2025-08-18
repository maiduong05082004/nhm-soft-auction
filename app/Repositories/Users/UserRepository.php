<?php

namespace App\Repositories\Users;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Users\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel(): string
    {
        return User::class;
    }

    public function emailVerificationNotification()
    {
        $this->model->getKey();
    }
}
