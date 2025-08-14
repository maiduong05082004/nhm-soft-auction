<?php

namespace App\Services;


interface BaseServiceInterface
{
    public function getAll(string $repo);

    public function getById(string $repo, $id);

    public function create(string $repo, array $data);

    public function update(string $repo, $id, array $data);

    public function delete(string $repo, $id);
}
