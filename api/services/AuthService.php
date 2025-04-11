<?php

declare(strict_types=1);

namespace api\services;

use api\interfaces\AuthServiceInterface;
use api\modules\employee\services\EmployeeService;
use api\models\LoginForm;
use Yii;

class AuthService implements AuthServiceInterface
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @throws \Exception
     */
    public function login(LoginForm $model): bool
    {
        $employee = $this->employeeService->findEmployeeByUsername($model->username);
        if ($employee && Yii::$app->security->validatePassword($model->password, $employee->password_hash)) {
            Yii::$app->user->login($employee, $model->rememberMe ? 3600 * 24 * 30 : 0);
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($employee->role);
            if ($role) {
                $auth->assign($role, $employee->id);
            }
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        Yii::$app->user->logout();
    }
}