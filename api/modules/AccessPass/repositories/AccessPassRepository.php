<?php

declare(strict_types=1);

namespace api\modules\AccessPass\repositories;

use api\modules\AccessPass\interfaces\AccessPassRepositoryInterface;
use api\modules\AccessPass\models\AccessPass;
use DateTime;
use yii\db\ActiveRecord;
use yii\db\Exception;

class AccessPassRepository implements AccessPassRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function save(AccessPass $accessPass): bool
    {
        return $accessPass->save();
    }

    public function findByTaskSiteAndEmployee(
        int $employeeId,
        int $siteId,
        int $workTaskId
    ): AccessPass|ActiveRecord|null {
        return AccessPass::find()
            ->where([
                'employee_id' => $employeeId,
                'construction_site_id' => $siteId,
                'work_task_id' => $workTaskId,
            ])->one();
    }

    public function findPassByParamsAndDate(
        int $employeeId,
        int $constructionSiteId,
        int $workTaskId,
        string $checkDateTime
    ): AccessPass|ActiveRecord|null {

        return AccessPass::find()
            ->where([
                'employee_id' => $employeeId,
                'construction_site_id' => $constructionSiteId,
                'work_task_id' => $workTaskId,
            ])
            ->andWhere(['<=', 'valid_from', $checkDateTime])
            ->andWhere(['>=', 'valid_to', $checkDateTime])
            ->one();
    }
}