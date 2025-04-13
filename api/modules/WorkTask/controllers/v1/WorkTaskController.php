<?php

declare(strict_types=1);

namespace api\modules\WorkTask\controllers\v1;

use api\modules\WorkTask\models\WorkTask;
use yii\rest\ActiveController;

class WorkTaskController extends ActiveController
{
    public $modelClass = WorkTask::class;

}