<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'app\modules\ConstructionSite\controllers';

    public function init()
    {
        parent::init();
        // Custom initialization code for the AccessPass module
    }
}