<?php
//
//declare(strict_types=1);
//
//namespace api\services;
//
//use api\interfaces\AuthServiceInterface;
//use api\models\LoginForm;
//use api\modules\Employee\models\Employee;
//use Yii;
//
//class AuthService implements AuthServiceInterface
//{
//    /**
//     * @param LoginForm $model
//     * @return bool
//     */
//    public function login(LoginForm $model): bool
//    {
//        $user = $this->getUser($model->username);
//        if (!$user || !Yii::$app->security->validatePassword($model->password, $user->password_hash)) {
//            return false;
//        }
//
//        return Yii::$app->user->login($user);
//    }
//
//    /**
//     * @return void
//     */
//    public function logout(): void
//    {
//        Yii::$app->user->logout();
//    }
//
//    /**
//     * @param string $username
//     * @return Employee|null
//     */
//    public function getUser(string $username): ?Employee
//    {
//        return Employee::findOne(['username' => $username]);
//    }
//}


declare(strict_types=1);

namespace api\services;

use api\interfaces\AuthServiceInterface;
use api\models\LoginForm;
use api\modules\Employee\models\Employee;
use Yii;

class AuthService implements AuthServiceInterface
{
    public function login(LoginForm $model): array
    {
        $user = $this->getUser($model->username);

        if (!$user || !Yii::$app->security->validatePassword($model->password, $user->password_hash)) {
            return [
                'success' => false,
                'statusCode' => 401,
                'message' => 'Invalid credentials',
            ];
        }

        if (Yii::$app->user->login($user)) {
            $authManager = Yii::$app->authManager;

            $roles = $authManager->getRolesByUser($user->id);
            $roleNames = array_keys($roles);

            $permissions = [];
            foreach ($authManager->getPermissionsByUser($user->id) as $permissionName => $permission) {
                $permissions[] = $permissionName;
            }

            $userData = $user->toArray(
                ['id', 'username', 'first_name', 'last_name', 'role', 'manager_id', 'created_at']
            );
            $userData['roles'] = $roleNames;
            $userData['permissions'] = $permissions;

            return [
                'success' => true,
                'data' => $userData,
            ];
        }

        return [
            'success' => false,
            'statusCode' => 500,
            'message' => 'User identity not set after login.',
        ];
    }

    public function logout(): void
    {
        Yii::$app->user->logout();
    }

    public function getUser(string $username): ?Employee
    {
        return Employee::findOne(['username' => $username]);
    }
}