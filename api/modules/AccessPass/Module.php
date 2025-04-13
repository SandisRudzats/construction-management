<?php

declare(strict_types=1);

namespace api\modules\AccessPass;


use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'api\modules\AccessPass\controllers\v1';

    public function init()
    {
        parent::init();
        // Custom initialization code for the AccessPass module
    }
}