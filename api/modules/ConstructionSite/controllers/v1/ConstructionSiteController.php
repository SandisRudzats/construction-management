<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\controllers\v1;

use api\modules\ConstructionSite\models\ConstructionSite;
use yii\rest\ActiveController;

class ConstructionSiteController extends ActiveController
{
    public $modelClass = ConstructionSite::class;

    public function actionSandis()
    {
        echo('sandis');
        dd('hello');
    }
}