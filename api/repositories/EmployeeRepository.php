<?php

declare(strict_types=1);

namespace api\repositories;

use api\interfaces\EmployeeRepositoryInterface;
use api\modules\employee\models\Employee;
use yii\db\Exception;
use yii\db\StaleObjectException;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function find(int $id): ?Employee
    {
        return Employee::findOne($id);
    }

    public function findAll(): array
    {
        return Employee::find()->all();
    }

    public function findByUsername(string $username): ?Employee
    {
        return Employee::findOne(['username' => $username]);
    }

    /**
     * @throws Exception
     */
    public function save(Employee $employee): bool
    {
        return $employee->save();
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }
}