<?php

declare(strict_types=1);

namespace api\modules\Employee\repositories;

use api\modules\Employee\interfaces\EmployeeRepositoryInterface;
use api\modules\Employee\models\Employee;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function find(int $id): ?Employee
    {
        return Employee::findOne($id);
    }

    /**
     * @throws Exception
     */
    public function save(Employee $employee): bool
    {
        return $employee->save();
    }

    /**
     * @throws Throwable | StaleObjectException
     */
    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }

    /**
     * @return Employee[]
     */
    public function getActiveEmployees(): array
    {
        return Employee::findAll(['active' => true]);
    }
}