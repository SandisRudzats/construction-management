<?php
declare(strict_types=1);

namespace api\modules\WorkTask\interfaces;

use api\modules\WorkTask\models\WorkTask;

interface WorkTaskServiceInterface
{

    public function createTask(array $data): ?WorkTask;

    public function updateTask(int $id, array $data): ?WorkTask;

    public function deleteTask(int $id): bool;

    /**
     * @return WorkTask[]
     */
    public function getTasksByEmployeeId(int $employeeId): array;
}