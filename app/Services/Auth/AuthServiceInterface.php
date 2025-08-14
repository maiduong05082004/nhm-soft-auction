<?php

namespace App\Services\Auth;

use App\Services\BaseServiceInterface;

interface AuthServiceInterface extends BaseServiceInterface
{
    public function verifyEmailUser($id, $hash): bool;
}
