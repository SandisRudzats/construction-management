<?php

use yii\db\Connection;
use yii\filters\Cors;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'as corsFilter' => [
        'class' => Cors::class,
        'cors' => [
            'Origin' => ['http://localhost:5174'], // <--- IMPORTANT: Check this port
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Max-Age' => 3600,
        ],
    ],
    'modules' => [
        'accessPass' => [
            'class' => 'app\modules\api\AccessPass\Module',
        ],
        'constructionSite' => [
            'class' => 'app\modules\api\ConstructionSite\Module',
        ],
        'employee' => [
            'class' => 'app\modules\api\Employee\Module',
        ],
        'workTask' => [
            'class' => 'app\modules\api\WorkTask\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'db' => [
            'class' => Connection::class,
            // TrustServerCertificate=yes - for development - not great, not terrible
            'dsn' => 'sqlsrv:Server=' . $_ENV['DB_HOST'] . ';Database=' . $_ENV['DB_NAME'] . ';Driver=ODBC Driver 18 for SQL Server;TrustServerCertificate=yes',
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
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
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'test'],
            ],
        ],
    ],
    'params' => $params,
];
