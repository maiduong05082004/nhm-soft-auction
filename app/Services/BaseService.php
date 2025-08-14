<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Repositories\BaseRepositoryInterface;

abstract class BaseService implements BaseServiceInterface
{
    protected array $repositories = [];

    /**
     * @throws ServiceException
     */
    public function __construct(array $repositories)
    {
        foreach ($repositories as $key => $repository) {
            // Kiểm tra xem repository có implements BaseRepositoryInterface không thì mới cho dùng nhé
            if (!in_array(BaseRepositoryInterface::class, class_implements($repository))) {
                throw new ServiceException("Repository {$key} must implement BaseRepositoryInterface.");
            }
            // Gán repository vào mảng
            $this->repositories[$key] = $repository;
        }
    }

    /**
     * Phương thức tiện ích để lấy repository
     * @throws ServiceException
     */
    protected function getRepository(string $name): BaseRepositoryInterface
    {
        return $this->repositories[$name] ?? throw new ServiceException("Repository {$name} not found.");
    }


    /**
     * Lấy tất cả các đối tượng
     */
    public function getAll(string $repo)
    {
        return $this->getRepository($repo)->getAll();
    }

    /**
     * Lấy đối tượng theo ID
     */
    public function getById(string $repo, $id)
    {
        return $this->getRepository($repo)->find($id);
    }

    /**
     * Tạo một đối tượng mới
     */
    public function create(string $repo, array $data)
    {
        return $this->getRepository($repo)->insertOne($data);
    }

    /**
     * Cập nhật đối tượng
     */
    public function update(string $repo, $id, array $data)
    {
        return $this->getRepository($repo)->updateOne($id, $data);
    }

    /**
     * Xoá đối tượng
     */
    public function delete(string $repo, $id)
    {
        return $this->getRepository($repo)->deleteOne($id);
    }

}
