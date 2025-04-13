<?php

declare(strict_types=1);

namespace api\modules\Employee\interfaces;

use api\modules\Employee\models\Employee;

interface EmployeeRepositoryInterface
{
    public function find(int $id): ?Employee;

    public function findAll(): array;

    public function findByUsername(string $username): ?Employee;

    public function save(Employee $employee): bool;

    public function delete(Employee $employee): bool;
}