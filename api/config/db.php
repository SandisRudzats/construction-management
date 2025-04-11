<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    // TrustServerCertificate=yes - for development - not great, not terrible
    'dsn' => 'sqlsrv:Server=' . $_ENV['DB_HOST'] . ';Database=' . $_ENV['DB_NAME'] . ';Driver=ODBC Driver 18 for SQL Server;TrustServerCertificate=yes',
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
];