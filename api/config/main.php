<?php

use api\controllers\AuthController;
use api\interfaces\AuthServiceInterface;
use api\modules\AccessPass\controllers\v1\AccessPassController;
use api\modules\AccessPass\interfaces\AccessPassRepositoryInterface;
use api\modules\AccessPass\interfaces\AccessPassServiceInterface;
use api\modules\AccessPass\Module as AccessPassModule;
use api\modules\AccessPass\repositories\AccessPassRepository;
use api\modules\AccessPass\services\AccessPassService;
use api\modules\ConstructionSite\controllers\v1\ConstructionSiteController;
use api\modules\ConstructionSite\interfaces\ConstructionSiteRepositoryInterface;
use api\modules\ConstructionSite\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\Module as ConstructionSiteModule;
use api\modules\ConstructionSite\repositories\ConstructionSiteRepository;
use api\modules\ConstructionSite\services\ConstructionSiteService;
use api\modules\Employee\controllers\v1\EmployeeController;
use api\modules\Employee\interfaces\EmployeeRepositoryInterface;
use api\modules\Employee\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use api\modules\Employee\Module as EmployeeModule;
use api\modules\Employee\repositories\EmployeeRepository;
use api\modules\Employee\services\EmployeeService;
use api\modules\WorkTask\controllers\v1\WorkTaskController;
use api\modules\WorkTask\interfaces\WorkTaskRepositoryInterface;
use api\modules\WorkTask\interfaces\WorkTaskServiceInterface;
use api\modules\WorkTask\Module as WorkTaskModule;
use api\modules\WorkTask\repositories\WorkTaskRepository;
use api\modules\WorkTask\services\WorkTaskService;
use api\services\AuthService;
use yii\filters\Cors;
use yii\log\FileTarget;
use yii\rbac\PhpManager;
use yii\web\JsonParser;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php',
);

$db = require __DIR__ . '/db.php';

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'container' => [
        'singletons' => [
            AuthServiceInterface::class => AuthService::class,

            EmployeeServiceInterface::class => EmployeeService::class,
            EmployeeRepositoryInterface::class => EmployeeRepository::class,

            ConstructionSiteServiceInterface::class => ConstructionSiteService::class,
            ConstructionSiteRepositoryInterface::class => ConstructionSiteRepository::class,

            WorkTaskServiceInterface::class => WorkTaskService::class,
            WorkTaskRepositoryInterface::class => WorkTaskRepository::class,

            AccessPassServiceInterface::class => AccessPassService::class,
            AccessPassRepositoryInterface::class => AccessPassRepository::class,
        ],
    ],
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
        'access-pass' => [
            'class' => AccessPassModule::class,
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
                    'class' => FileTarget::class,
                    'levels' => ['trace', 'error', 'warning'],
                    'categories' => ['yii\web\UrlManager::parseRequest'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        'authManager' => [
            'class' => PhpManager::class,
            'defaultRoles' => ['employee', 'manager', 'admin'],
            'itemFile' => '@common/rbac/items.php',
            'assignmentFile' => '@common/rbac/assignments.php',
            'ruleFile' => '@common/rbac/rules.php',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'auth/login' => 'auth/login',
                'auth/logout' => 'auth/logout',
                'v1/construction-site' => 'construction-site/construction-site/',
                'v1/employee/create' => 'employee/employee/create',
                'v1/construction-site/create' => 'construction-site/construction-site/create',
                'PUT v1/employee/<id:\d+>' => 'employee/employee/update',
                'GET v1/employee/me' => 'employee/employee/view-self',
                'GET v1/employee/active-employees' => 'employee/employee/active-employees',
                'DELETE v1/construction-site/<id:\d+>' => 'construction-site/construction-site/delete',
                'PUT v1/construction-site/<id:\d+>' => 'construction-site/construction-site/update',
                'GET v1/construction-site/<id:\d+>/site-work-tasks' => 'construction-site/construction-site/site-work-tasks',
                'PUT v1/work-task/<id:\d+>' => 'work-task/work-task/update',
                'GET v1/work-task/employee-tasks' => 'work-task/work-task/employee-tasks',
                'DELETE v1/work-task/<id:\d+>' => 'work-task/work-task/delete',
                'POST v1/work-task/create' => 'work-task/work-task/create',
                'POST v1/access-passes/create' => 'access-pass/access-pass/create',
                'PUT v1/access-passes/update-from-task' => 'access-pass/access-pass/update-from-task',
            ],
        ],
        'cors' => [
            'class' => Cors::class,
            'cors' => [
                // strictly in app for now
                'Origin' => ['http://localhost:5173'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['X-Requested-With', 'Content-Type', 'Authorization'],
                'Access-Control-Allow-Origin' => ['http://localhost:5173'],
            ],
        ],
    ],
    'controllerMap' => [
        'auth' => [
            'class' => AuthController::class,
        ],
        'v1/employee' => [
            'class' => EmployeeController::class,
        ],
        'v1/construction-site' => [
            'class' => ConstructionSiteController::class,
        ],
        'v1/work-task' => [
            'class' => WorkTaskController::class,
        ],
        'v1/access-pass' => [
            'class' => AccessPassController::class,
        ],
    ],
];
