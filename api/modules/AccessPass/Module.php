<?php

declare(strict_types=1);

namespace app\modules\AccessPass;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'app\modules\AccessPass\controllers';

    public function init()
    {
        parent::init();
        // Custom initialization code for the AccessPass module
    }
}