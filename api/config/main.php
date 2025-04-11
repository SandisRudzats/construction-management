<?php

use api\controllers\AuthController;
use api\controllers\v1\AccessPassController;
use api\modules\ConstructionSite\controllers\v1\ConstructionSiteController;
use api\modules\ConstructionSite\services\ConstructionSiteService;
use api\modules\Employee\controllers\v1\EmployeeController;
use api\modules\employee\models\Employee;
use api\modules\Employee\services\EmployeeService;
use api\modules\WorkTask\controllers\v1\WorkTaskController;
use api\modules\WorkTask\services\WorkTaskService;
use api\repositories\AccessPassRepository;
use api\repositories\ConstructionSiteRepository;
use api\repositories\EmployeeRepository;
use api\repositories\WorkTaskRepository;
use api\services\AccessPassService;
use api\services\AuthService;
use yii\rbac\PhpManager;
use yii\rest\UrlRule;
use yii\web\JsonParser;
use \api\modules\WorkTask\Module as WorkTaskModule;
use \api\modules\ConstructionSite\Module as ConstructionSiteModule;
use \api\modules\Employee\Module as EmployeeModule;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'employee' => [
            'class' => EmployeeModule::class,
        ],
        'construction-site' => [
            'class' => ConstructionSiteModule::class,
        ],
        'work-task' => [
            'class' => WorkTaskModule::class,
        ],
    ],
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'format' => Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && Yii::$app->request->get('prettyPrint')) {
                    $response->format = Response::FORMAT_JSON;
                    $response->options = ['prettyPrint' => true];
                }
            },
        ],
        'user' => [
            'identityClass' => Employee::class,
            'enableAutoLogin' => false,
            'enableSession' => true,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => UrlRule::class, 'controller' => 'v1/employee'],
                ['class' => UrlRule::class, 'controller' => 'v1/construction-site'],
                ['class' => UrlRule::class, 'controller' => 'v1/work-task'],
                ['class' => UrlRule::class, 'controller' => 'v1/access-pass'],
                'auth/login' => 'auth/login',
                'auth/logout' => 'auth/logout',
                'access-pass/validate' => 'access-pass/validate',
            ],
        ],
        // ... other components ...
        'authManager' => [
            'class' => PhpManager::class,
        ],
        'authService' => [
            'class' => AuthService::class,
            '__construct()' => [Yii::$app->get('employeeService')],
        ],
        'employeeService' => [
            'class' => EmployeeService::class,
            '__construct()' => [Yii::$app->get('employeeRepository')],
        ],
        'employeeRepository' => [
            'class' => EmployeeRepository::class,
        ],
        'constructionSiteService' => [
            'class' => ConstructionSiteService::class,
            '__construct()' => [Yii::$app->get('constructionSiteRepository')],
        ],
        'constructionSiteRepository' => [
            'class' => ConstructionSiteRepository::class,
        ],
        'workTaskService' => [
            'class' => WorkTaskService::class,
            '__construct()' => [Yii::$app->get('workTaskRepository')],
        ],
        'workTaskRepository' => [
            'class' => WorkTaskRepository::class,
        ],
        'accessPassService' => [
            'class' => AccessPassService::class,
            '__construct()' => [Yii::$app->get('accessPassRepository')],
        ],
        'accessPassRepository' => [ // ADDED THIS
            'class' => AccessPassRepository::class,
        ],
        'authorizationService' => [ // ADDED THIS
            'class' => AuthService::class,
            '__construct()' => [
                Yii::$app->get('authManager'),
                Yii::$app->get('accessPassService'),
                Yii::$app->get('employeeService'),
            ],
        ],
    ],
    'controllerMap' => [
        'auth' => [
            'class' => AuthController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
        'access-pass' => [
            'class' => AccessPassController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
        'v1/employee' => [
            'class' => EmployeeController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
        'v1/construction-site' => [
            'class' => ConstructionSiteController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
        'v1/work-task' => [
            'class' => WorkTaskController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
        'v1/access-pass' => [
            'class' => AccessPassController::class,
            'as corsFilter' => Yii::$app->get('corsFilter'),
        ],
    ],
];