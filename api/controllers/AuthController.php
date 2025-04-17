<?php
//
declare(strict_types=1);

namespace api\controllers;

use api\interfaces\AuthServiceInterface;
use api\models\LoginForm;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
//
//class AuthController extends Controller
//{
//
//    public function __construct(
//        $id,
//        $module,
//        private AuthServiceInterface $authService,
//        $config = []
//    ) {
//        parent::__construct($id, $module, $config);
//    }
//
//    /**
//     * @return Response|array
//     */
//    public function actionLogin(): Response|array
//    {
//        $model = new LoginForm();
//        $model->load(Yii::$app->request->post(), '');
//
//        if ($model->validate()) {
//            if ($this->authService->login($model)) {
//                $user = Yii::$app->user->identity;
//                if ($user !== null) {
//                    $authManager = Yii::$app->authManager;
//                    $roles = $authManager->getRolesByUser($user->id);
//
//                    $roleNames = array_keys($roles);
//
//                    $permissions = [];
//                    foreach ($authManager->getPermissionsByUser($user->id) as $permissionName => $permission) {
//                        $permissions[] = $permissionName;
//                    }
//
//                    $userData = $user->toArray(
//                        ['id', 'username', 'first_name', 'last_name', 'role', 'manager_id', 'created_at']
//                    );
//
//                    $userData['roles'] = $roleNames;
//                    $userData['permissions'] = $permissions;
//
//                    return $userData;
//                } else {
//                    Yii::$app->response->statusCode = 500;
//                    return ['message' => 'User identity not set after login.'];
//                }
//            } else {
//                Yii::$app->response->statusCode = 401;
//                return ['message' => 'Invalid credentials'];
//            }
//        } else {
//            Yii::$app->response->statusCode = 422;
//            return $model->getErrors();
//        }
//    }
//
//    public function actionLogout(): Response
//    {
//        $this->authService->logout();
//        Yii::$app->response->statusCode = 204;
//        return Yii::$app->response;
//    }
//}
//
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
            $result = $this->authService->login($model);

            if ($result['success']) {
                return $result['data'];
            } else {
                Yii::$app->response->statusCode = $result['statusCode'];
                return ['message' => $result['message']];
            }
        } else {
            Yii::$app->response->statusCode = 422;
            return $model->getErrors();
        }
    }

    public function actionLogout(): Response
    {
        $this->authService->logout();
        Yii::$app->response->statusCode = 204;
        return Yii::$app->response;
    }
}
