<?php

declare(strict_types=1);

namespace api\modules\Employee\services;

use api\helpers\RequestValidationHelper;
use api\modules\Employee\interfaces\EmployeeRepositoryInterface;
use api\modules\Employee\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\Exception as DbException;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;

readonly class EmployeeService implements EmployeeServiceInterface
{
    public function __construct(
        private EmployeeRepositoryInterface $repository,
        private RequestValidationHelper $requestValidationHelper
    ) {
    }

    /**
     * @throws Exception|DbException
     */
    public function createEmployee(array $data): Employee
    {
        $this->requestValidationHelper->validateRequiredFields($data, Employee::REQUIRED_FIELDS);

        $employee = new Employee();
        $employee->username = $data['username'];
        $employee->first_name = $data['first_name'];
        $employee->last_name = $data['last_name'];
        $employee->setPassword($data['password']);
        $employee->role = $data['role'];
        $employee->birth_date = $data['birth_date'] ?? null;
        $employee->access_level = $data['access_level'] ?? 1;
        $employee->manager_id = $data['manager_id'] ?? null;

        if (!$employee->validate()) {
            throw new Exception('Validation failed: ' . json_encode($employee->errors));
        }

        if (!$this->repository->save($employee)) {
            throw new Exception('Failed to save employee.');
        }

        $this->assignRole($employee, $data['role']);

        return $employee;
    }

    /**
     * @throws Exception | \Exception
     */
    public function updateEmployee(Employee $employee, array $data): ?Employee
    {
        $this->requestValidationHelper->validateRequiredFields($data, Employee::REQUIRED_FIELDS);
        $auth = Yii::$app->authManager;

        $newRoleName = $data['role'] ?? null;
        if ($newRoleName && $newRoleName !== $employee->role) {
            $oldRole = $auth->getRole($employee->role);
            if ($oldRole) {
                $auth->revoke($oldRole, $employee->id);
            }

            $newRole = $auth->getRole($newRoleName);
            if ($newRole === null) {
                throw new Exception('Invalid role: ' . $newRoleName);
            }
            $auth->assign($newRole, $employee->id);
            $employee->role = $newRoleName;
        }

        $employee->load($data, '');
        if (!$employee->validate()) {
            throw new Exception('Validation failed: ' . json_encode($employee->errors));
        }

        if (!$this->repository->save($employee)) {
            throw new Exception('Failed to update employee.');
        }

        return $employee;
    }

    /**
     * @throws Throwable | Exception | StaleObjectException | ForbiddenHttpException
     */
    public function deleteEmployee(int $id): bool
    {
        $employee = $this->repository->find($id);

        if (!$employee) {
            throw new Exception('Employee not found.');
        }
        if (!Yii::$app->user->can('deleteEmployee', ['employee' => $employee])) {
            throw new ForbiddenHttpException("You don't have permission to delete this employee.");
        }

        if (!$this->repository->delete($employee)) {
            throw new Exception('Failed to delete employee.');
        }

        return true;
    }

    /**
     * @return Employee[]
     */
    public function getActiveEmployees(): array
    {
        return $this->repository->getActiveEmployees();
    }

    public function getEmployeeById(mixed $id): ?Employee
    {
        return $this->repository->find($id);
    }

    /**
     * @throws Exception | \Exception
     */
    private function assignRole(Employee $employee, string $roleName): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole(strtolower($roleName));
        if ($role === null) {
            throw new Exception('Invalid role: ' . $roleName);
        }
        $auth->assign($role, $employee->id);
    }
}