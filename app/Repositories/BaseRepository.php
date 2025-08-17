<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * @throws BindingResolutionException
     */
    private function setModel(): void
    {
        $this->model = app()->make($this->getModel());
    }

    abstract public function getModel(): string;

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->query();
    }

    public function getAll(array $conditions = [], array $with = [], array $joins = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($joins)) {
            foreach ($joins as $join) {
                // ['table' => 'profiles', 'first' => 'users.id', 'operator' => '=', 'second' => 'profiles.user_id']
                $query->join($join['table'], $join['first'], $join['operator'] ?? '=', $join['second']);
            }
        }

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    public function find($id): ?Model
    {
        return $this->model::query()->find($id);
    }

    public function insertOne(array $attributes): Model
    {
        return $this->model::query()->create($attributes);
    }

    public function insertMany(array $data): int
    {
        return $this->model::query()->insert($data); // Trả về true/false, không tạo timestamps
    }

    public function updateOne($id, array $attributes): ?Model
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($attributes);
            return $model;
        }
        return null;
    }

    public function updateMany(array $conditions, array $attributes): int
    {
        return $this->model::query()->where($conditions)->update($attributes);
    }

    public function deleteOne($id): bool
    {
        $model = $this->find($id);
        if ($model) {
            return (bool) $model->delete();
        }
        return false;
    }

    public function deleteMany(array $conditions): int
    {
        return $this->model::query()->where($conditions)->delete();
    }

    public function restoreOne($id): bool
    {
        // Kiểm tra nếu model sử dụng SoftDeletes
        if (!in_array(SoftDeletes::class, class_uses($this->model))) {
            throw new RepositoryException("Model does not use SoftDeletes.", 500);
        }
        $record = $this->model::query()->withTrashed()->find($id);
        return $record ? $record->restore() : false;
    }

    public function restoreMany(array $conditions): int
    {
        if (!in_array(SoftDeletes::class, class_uses($this->model))) {
            throw new RepositoryException("Model does not use SoftDeletes.", 500);
        }

        $query = $this->model::query()->withTrashed()->newQuery();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        return $query->restore();
    }
}
