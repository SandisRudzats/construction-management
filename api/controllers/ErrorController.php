<?php

declare(strict_types=1);

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use yii\web\HttpException;

class ErrorController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'renderer' => function ($exception) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $response = [
                        'status' => ($exception instanceof HttpException) ? $exception->statusCode : 500,
                        'message' => $exception->getMessage(),
                    ];
                    if (YII_DEBUG) {
                        $response['trace'] = $exception->getTraceAsString();
                    }
                    return $response;
                },
            ],
        ];
    }
}