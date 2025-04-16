<?php

declare(strict_types=1);

namespace api\modules\Employee\interfaces;

use api\modules\Employee\models\Employee;

interface EmployeeServiceInterface
{
    public function createEmployee(array $data): ?Employee;

    public function updateEmployee(int $id, array $data): ?Employee;

    public function deleteEmployee(int $id): bool;

    /**
     * @return Employee[]
     */
    public function getActiveEmployees(): array;

    public function getEmployeeById(int $id): ?Employee;
}