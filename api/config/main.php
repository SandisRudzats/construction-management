<?php

use api\controllers\AuthController;
use api\controllers\v1\AccessPassController;
use api\interfaces\AccessPassRepositoryInterface;
use api\interfaces\AccessPassServiceInterface;
use api\interfaces\ConstructionSiteRepositoryInterface;
use api\interfaces\ConstructionSiteServiceInterface;
use api\interfaces\EmployeeRepositoryInterface;
use api\interfaces\EmployeeServiceInterface;
use api\interfaces\WorkTaskRepositoryInterface;
use api\interfaces\WorkTaskServiceInterface;
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
use yii\filters\Cors;
use yii\log\FileTarget;
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
            // Authentication Services
            \api\interfaces\AuthServiceInterface::class => \api\services\AuthService::class,

            // Employee Services and Repositories
            EmployeeServiceInterface::class => EmployeeService::class,
            EmployeeRepositoryInterface::class => EmployeeRepository::class,

            // Construction Site Services and Repositories
            ConstructionSiteServiceInterface::class => ConstructionSiteService::class,
            ConstructionSiteRepositoryInterface::class => ConstructionSiteRepository::class,

            // Work Task Services and Repositories
            WorkTaskServiceInterface::class => WorkTaskService::class,
            WorkTaskRepositoryInterface::class => WorkTaskRepository::class,

            // Access Pass Services and Repositories
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
                    'categories' => ['yii\web\UrlManager::parseRequest'], // Log URL manager routing
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        'authManager' => [
            'class' => PhpManager::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // Define the route for v1/employee, making sure it maps correctly to the v1 namespace
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/employee', // Use the v1 namespace for the employee controller
                    'pluralize' => false, // Disabling pluralization
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'employee/employee',
                        'v1/employee',
                        'work-task/work-task',
                        'access-pass/access-pass',
                    ],
                    'pluralize' => false,
                ],
//                'auth/login' => 'auth/login',
//                'auth/logout' => 'auth/logout',
//                'employee/hello' => 'employee/hello',
//                'access-pass/validate' => 'access-pass/validate',
//                'employee/employee', // maps to modules/Employee/controllers/v1/EmployeeController
            ],
        ],
        'db' => $db, // Ensure this line is present
        'corsFilter' => [
            'class' => Cors::class,
            'cors' => [
                // todo : change to locahost before final version
//                'Origin' => ['http://localhost:5174'],
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Per-Page',
                    'X-Pagination-Total-Count'
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'auth' => [
            'class' => AuthController::class,
        ],
        'access-pass' => [
            'class' => AccessPassController::class,
        ],
//        'v1/employee' => [
//            'class' => EmployeeController::class,
//        ],
//        'v1/construction-site' => [
//            'class' => ConstructionSiteController::class,
//        ],
//        'v1/work-task' => [
//            'class' => WorkTaskController::class,
//        ],
//        'v1/access-pass' => [
//            'class' => AccessPassController::class,
//        ],
    ],
];