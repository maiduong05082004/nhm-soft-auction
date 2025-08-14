<?php

namespace App\Repositories\Users;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Products\ProductRepositoryInterface;

class UserRepository extends BaseRepository implements ProductRepositoryInterface
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
