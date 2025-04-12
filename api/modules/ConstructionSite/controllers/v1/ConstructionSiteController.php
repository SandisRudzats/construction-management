<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\models\ConstructionSite;
use yii\rest\ActiveController;

class ConstructionSiteController extends ActiveController
{
    public $modelClass = ConstructionSite::class;
}