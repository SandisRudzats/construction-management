<?php

declare(strict_types=1);

namespace api\controllers;

use api\models\LoginForm;
use api\interfaces\AuthServiceInterface;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{

    public function __construct($id, $module, private AuthServiceInterface $authService, $config = [])
    {
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
                return Yii::$app->user->identity->toArray(['id', 'username', 'first_name', 'last_name', 'role']
                ); // Return user info on successful login
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