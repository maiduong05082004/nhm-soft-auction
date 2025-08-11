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

}
