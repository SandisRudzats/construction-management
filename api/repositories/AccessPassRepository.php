<?php

declare(strict_types=1);

namespace api\repositories;

use api\interfaces\AccessPassRepositoryInterface;
use api\models\AccessPass;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class AccessPassRepository implements AccessPassRepositoryInterface
{
    public function find(int $id): ?AccessPass
    {
        return AccessPass::findOne($id);
    }

    public function findAll(): array
    {
        return AccessPass::find()->all();
    }

    public function findByEmployeeAndSiteAndDate(int $employeeId, int $constructionSiteId, string $date): array
    {
        return AccessPass::find()
            ->where(['employee_id' => $employeeId, 'construction_site_id' => $constructionSiteId])
            ->andWhere(['<=', 'valid_from', $date])
            ->andWhere(['>=', 'valid_until', $date])
            ->all();
    }

    /**
     * @throws Exception
     */
    public function save(AccessPass $accessPass): bool
    {
        return $accessPass->save();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete(AccessPass $accessPass): bool
    {
        return $accessPass->delete();
    }
}