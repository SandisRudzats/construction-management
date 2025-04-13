<?php

declare(strict_types=1);

namespace api\modules\WorkTask\repositories;

use api\modules\WorkTask\interfaces\WorkTaskRepositoryInterface;
use api\modules\workTask\models\WorkTask;

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

    public function save(WorkTask $workTask): bool
    {
        return $workTask->save();
    }

    public function delete(WorkTask $workTask): bool
    {
        return $workTask->delete();
    }
}