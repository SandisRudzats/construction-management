<?php

declare(strict_types=1);

namespace api\interfaces;

use api\modules\employee\models\Employee;

interface EmployeeServiceInterface
{
    public function findEmployee(int $id): ?Employee;
    public function findAllEmployees(): array;
    public function findEmployeeByUsername(string $username): ?Employee;
    public function createEmployee(array $data): ?Employee;
    public function updateEmployee(int $id, array $data): ?Employee;
    public function deleteEmployee(int $id): bool;
}