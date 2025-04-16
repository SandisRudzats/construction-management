<?php

declare(strict_types=1);

namespace api\services;

use api\interfaces\AuthServiceInterface;
use api\models\LoginForm;
use api\modules\Employee\models\Employee;
use Yii;
use yii\rbac\Role;

class AuthService implements AuthServiceInterface
{
    /**
     * @param LoginForm $model
     * @return bool
     */
    public function login(LoginForm $model): bool
    {
        $user = $this->getUser($model->username);
        if (!$user || !Yii::$app->security->validatePassword($model->password, $user->password_hash)) {
            return false;
        }

        return Yii::$app->user->login($user);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        Yii::$app->user->logout();
    }

    /**
     * @param string $username
     * @return Employee|null
     */
    public function getUser(string $username): ?Employee
    {
        return Employee::findOne(['username' => $username]);
    }
}
