<?php

declare(strict_types=1);

namespace console\controllers;

use Yii;
use yii\console\Controller;
use api\modules\employee\models\Employee;
use yii\db\Exception;

class DatabaseSeederController extends Controller
{
    private const ROLE_ADMIN = 'admin';
    private const ROLE_EMPLOYEE = 'employee';
    private const ROLE_MANAGER = 'manager';

    private const ROLES_MAPPING = [
        1 => self::ROLE_EMPLOYEE,
        2 => self::ROLE_ADMIN,
        3 => self::ROLE_MANAGER,
    ];

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionCreateUser(
        string $username,
        string $password,
        string $firstName = 'New',
        string $lastName = 'User',
        int $identifier = 1
    ): void {
        $employee = new Employee();
        $employee->username = $username;
        $employee->first_name = $firstName;
        $employee->last_name = $lastName;

        $role = self::ROLES_MAPPING[$identifier] ?? null;

        if (!$role) {
            $message = sprintf(
                "Invalid role identifier: %s, must be one of these - 1 - employee, 2 - admin or 3 - manager \n",
                $identifier
            );

            $this->stdout($message);
            return;
        }

        $employee->role = $role;
        $employee->password_hash = Yii::$app->security->generatePasswordHash($password);

        if ($employee->save()) {
            $this->stdout("User '{$username}' created successfully with role '{$role}'.\n");
        } else {
            $this->stderr("Failed to create user.\n");
            $this->stderr(print_r($employee->errors, true));
        }
    }
}