<?php

declare(strict_types=1);

namespace api\controllers;

use api\interfaces\AuthServiceInterface;
use api\models\LoginForm;
use api\modules\Employee\interfaces\EmployeeServiceInterface;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{

    public function __construct(
        $id,
        $module,
        private AuthServiceInterface $authService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @return Response|array
     */
    public function actionLogin(): Response|array
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post(), '');

        if ($model->validate()) {
            if ($this->authService->login($model)) {
                $user = Yii::$app->user->identity;
                if ($user !== null) {
                    $authManager = Yii::$app->authManager;
                    $roles = $authManager->getRolesByUser($user->id);

                    // Convert roles to a simple array of names
                    $roleNames = array_keys($roles);

                    // Get permissions for the user (more complex, needs iteration)
                    $permissions = [];
                    foreach ($authManager->getPermissionsByUser($user->id) as $permissionName => $permission) {
                        $permissions[] = $permissionName;
                    }

                    $userData = $user->toArray(
                        ['id', 'username', 'first_name', 'last_name', 'role', 'manager_id', 'created_at']
                    );

                    $userData['roles'] = $roleNames; // Add roles to the response
                    $userData['permissions'] = $permissions; // Add permissions

                    return $userData; // Return user info with roles and permissions on successful login
                } else {
                    Yii::$app->response->statusCode = 500; // Internal Server Error
                    return ['message' => 'User identity not set after login.']; // More specific error
                }
            } else {
                Yii::$app->response->statusCode = 401; // Unauthorized
                return ['message' => 'Invalid credentials'];
            }
        } else {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return $model->getErrors();
        }
    }

    public function actionLogout(): Response
    {
        $this->authService->logout();
        Yii::$app->response->statusCode = 204; // No Content (successful logout)
        return Yii::$app->response;
    }
}

