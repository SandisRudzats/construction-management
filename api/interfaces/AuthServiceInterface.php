<?php

declare(strict_types=1);

namespace api\interfaces;

use api\models\LoginForm;

interface AuthServiceInterface
{
    public function login(LoginForm $model): bool;

    public function logout(): void;
}