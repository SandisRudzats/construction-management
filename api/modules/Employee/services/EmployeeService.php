<?php

declare(strict_types=1);

namespace api\modules\Employee\services;

use api\interfaces\EmployeeRepositoryInterface;
use api\interfaces\EmployeeServiceInterface;
use api\modules\employee\models\Employee;
use yii\db\Exception;

class EmployeeService implements EmployeeServiceInterface
{
    private EmployeeRepositoryInterface $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function findEmployee(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function findAllEmployees(): array
    {
        return $this->employeeRepository->findAll();
    }

    public function findEmployeeByUsername(string $username): ?Employee
    {
        return $this->employeeRepository->findByUsername($username);
    }

    /**
     * @throws Exception
     */
    public function createEmployee(array $data): ?Employee
    {
        $employee = new Employee();
        $employee->load($data, ''); // Load attributes from the array
        if ($employee->save()) {
            return $employee;
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function updateEmployee(int $id, array $data): ?Employee
    {
        $employee = $this->employeeRepository->find($id);
        if ($employee) {
            $employee->load($data, '');
            if ($employee->save()) {
                return $employee;
            }
        }
        return null;
    }

    public function deleteEmployee(int $id): bool
    {
        $employee = $this->employeeRepository->find($id);
        if ($employee) {
            return $this->employeeRepository->delete($employee);
        }
        return false;
    }
}