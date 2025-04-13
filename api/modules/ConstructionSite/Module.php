<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'api\modules\ConstructionSite\controllers\v1';

    public function init()
    {
        parent::init();
        // Custom initialization code for the AccessPass module
    }
}