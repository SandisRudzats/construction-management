<?php

declare(strict_types=1);

namespace api\controllers;

use api\interfaces\AuthServiceInterface;
use api\models\LoginForm;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly AuthServiceInterface $authService,
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
