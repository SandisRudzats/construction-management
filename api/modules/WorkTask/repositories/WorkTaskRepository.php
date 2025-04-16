<?php

declare(strict_types=1);

namespace api\modules\WorkTask\repositories;

use api\modules\WorkTask\interfaces\WorkTaskRepositoryInterface;
use api\modules\workTask\models\WorkTask;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class WorkTaskRepository implements WorkTaskRepositoryInterface
{
    public function find(int $id): ?WorkTask
    {
        return WorkTask::findOne($id);
    }

    public function findAll(): array
    {
        return WorkTask::find()->all();
    }

    /**
     * @throws Exception
     */
    public function save(WorkTask $workTask): bool
    {
        return $workTask->save();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete(WorkTask $workTask): bool
    {
        return $workTask->delete();
    }

    public function getSiteIdsByEmployeeIdFromWorkTasks(int $id): array
    {
        return WorkTask::find()->where(['employee_id' => $id])->distinct()->column('construction_site_id');
    }

    /**
     * @return WorkTask[]
     */
    public function getTasksByEmployeeId(int $employeeId): array
    {
        return WorkTask::find()->where(['employee_id' => $employeeId])->all();
    }
}