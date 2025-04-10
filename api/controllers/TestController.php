<?php
declare(strict_types=1);

namespace api\controllers;

use yii\rest\Controller;
use yii\web\Response;

class TestController extends Controller
{
    public function actionHello()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'message' => 'Hello from the backend!',
            'timestamp' => time(),
        ];
    }
}