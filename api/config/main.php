<?php

use api\controllers\AuthController;
use api\modules\AccessPass\controllers\v1\AccessPassController;
use api\modules\AccessPass\interfaces\AccessPassRepositoryInterface;
use api\modules\AccessPass\interfaces\AccessPassServiceInterface;
use api\modules\AccessPass\Module as AccessPassModule;
use api\modules\AccessPass\repositories\AccessPassRepository;
use api\modules\ConstructionSite\interfaces\ConstructionSiteRepositoryInterface;
use api\modules\ConstructionSite\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\Module as ConstructionSiteModule;
use api\modules\ConstructionSite\repositories\ConstructionSiteRepository;
use api\modules\ConstructionSite\services\ConstructionSiteService;
use api\modules\Employee\interfaces\EmployeeRepositoryInterface;
use api\modules\Employee\interfaces\EmployeeServiceInterface;
use api\modules\Employee\models\Employee;
use api\modules\Employee\Module as EmployeeModule;
use api\modules\Employee\repositories\EmployeeRepository;
use api\modules\Employee\services\EmployeeService;
use api\modules\WorkTask\interfaces\WorkTaskRepositoryInterface;
use api\modules\WorkTask\interfaces\WorkTaskServiceInterface;
use api\modules\WorkTask\Module as WorkTaskModule;
use api\modules\WorkTask\repositories\WorkTaskRepository;
use api\modules\WorkTask\services\WorkTaskService;
use api\services\AccessPassService;
use console\commands\RbacController;
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
                    'categories' => ['yii\web\UrlManager::parseRequest'], // Log URL manager routing
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        // todo: setapot kkādu tokenu frontendā - ar ko sūtīs rekvestus
        // todo:: vajadzes iznemt
        'authManager' => [
            'class' => PhpManager::class,
            'defaultRoles' => ['employee', 'manager', 'admin'],
            'itemFile' => '@common/rbac/items.php',
            'assignmentFile' => '@common/rbac/assignments.php',
            'ruleFile' => '@common/rbac/rules.php',
        ],
        // todo:: cleanappo routes ko nevajadzēs
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'auth/login' => 'auth/login',
                'auth/logout' => 'auth/logout',
                'v1/construction-site' => 'construction-site/construction-site/',
                // todo :: atstāju sev piemēram priekš custom stuff
                'v1/construction-site/sandis' => 'construction-site/construction-site/sandis',
                'v1/employee/create' => 'employee/employee/create',
                'v1/employee/' => 'employee/employee/',
//                'POST v1/employee' => 'employee/employee/create',
                'v1/access-pass' => 'access-pass/access-pass/',
                'v1/work-task' => 'work-task/work-task/',
            ],
        ],
        'cors' => [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['X-Requested-With', 'Content-Type', 'Authorization'],
                'Access-Control-Allow-Origin' => ['*'],
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
        // Registering controllers for version 1 of the modules
        'v1/employee' => [
            'class' => \api\modules\Employee\controllers\v1\EmployeeController::class,
        ],
        'v1/construction-site' => [
            'class' => \api\modules\ConstructionSite\controllers\v1\ConstructionSiteController::class,
        ],
        'v1/work-task' => [
            'class' => \api\modules\WorkTask\controllers\v1\WorkTaskController::class,
        ],
        'v1/access-pass' => [
            'class' => \api\modules\AccessPass\controllers\v1\AccessPassController::class,
        ],
    ],
];
