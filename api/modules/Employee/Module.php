<?php

declare(strict_types=1);

namespace api\modules\Employee;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'api\modules\Employee\controllers\v1';

    public function init(): void
    {
        parent::init();
    }
}