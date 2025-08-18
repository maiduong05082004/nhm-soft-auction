<?php

namespace App\Services\Auth;

use App\Services\BaseServiceInterface;

interface AuthServiceInterface extends BaseServiceInterface
{
    public function verifyEmailUser($id, $hash): bool;

    public function getInfoAuth();

    public function updateAuthUser(array $data): bool;

    public function getSumTransaction();
}
