<?php

namespace App\Services\Auth;

use App\Services\BaseServiceInterface;
use Illuminate\Contracts\Auth\Authenticatable;

interface AuthServiceInterface extends BaseServiceInterface
{
    public function verifyEmailUser($id, $hash): bool;

    public function getInfoAuth();
}
