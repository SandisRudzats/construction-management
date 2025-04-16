<?php

declare(strict_types=1);

namespace api\modules\AccessPass\interfaces;

use api\modules\AccessPass\models\AccessPass;
use DateTime;
use yii\db\ActiveRecord;

interface AccessPassRepositoryInterface
{
    public function save(AccessPass $accessPass): bool;

    public function findByTaskSiteAndEmployee(
        int $employeeId,
        int $siteId,
        int $workTaskId,
    ): AccessPass|ActiveRecord|null;

    public function findPassByParamsAndDate(
        int $employeeId,
        int $constructionSiteId,
        int $workTaskId,
        DateTime $checkDateTime
    ): AccessPass|ActiveRecord|null;

}