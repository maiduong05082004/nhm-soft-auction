<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function getQueryBuilder(): \Illuminate\Database\Query\Builder;

    public function query() : \Illuminate\Database\Eloquent\Builder;

    public function getAll(array $conditions = [], array $with = [], array $joins = []): Collection;

    public function find($id): ?Model;

    public function insertOne(array $attributes): Model;

    public function insertMany(array $data): int;

    public function updateOne($id, array $attributes): ?Model;

    public function updateMany(array $conditions, array $attributes): int;

    public function deleteOne($id): bool;

    public function deleteMany(array $conditions): int;

    /**
     * @throws RepositoryException
     */
    public function restoreOne($id): bool;

    /**
     * @throws RepositoryException
     */
    public function restoreMany(array $conditions): int;

}
