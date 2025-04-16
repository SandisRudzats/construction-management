<?php

declare(strict_types=1);

namespace api\modules\Employee\interfaces;

use api\modules\Employee\models\Employee;

interface EmployeeServiceInterface
{
    public function createEmployee(array $data): ?Employee;
    public function updateEmployee(Employee $employee, array $data): ?Employee;
    public function deleteEmployee(int $id): bool;
}