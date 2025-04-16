<?php

declare(strict_types=1);

namespace api\modules\WorkTask\interfaces;

use api\modules\workTask\models\WorkTask;

interface WorkTaskRepositoryInterface
{
    public function find(int $id): ?WorkTask;

    public function findAll(): array;

    public function save(WorkTask $workTask): bool;

    public function delete(WorkTask $workTask): bool;

    public function getSiteIdsByEmployeeIdFromWorkTasks(int $id): array;

    /**
     * @return WorkTask[]
     */
    public function getTasksByEmployeeId(int $employeeId): array;
}