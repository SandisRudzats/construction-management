<?php

declare(strict_types=1);

namespace api\modules\WorkTask\services;

use api\helpers\RequestValidationHelper;
use api\modules\WorkTask\interfaces\WorkTaskRepositoryInterface;
use api\modules\WorkTask\interfaces\WorkTaskServiceInterface;
use api\modules\WorkTask\models\WorkTask;
use Exception;
use yii\web\BadRequestHttpException;

readonly class WorkTaskService implements WorkTaskServiceInterface
{

    public function __construct(
        private WorkTaskRepositoryInterface $workTaskRepository,
        private RequestValidationHelper $requestValidationHelper
    ) {
    }

    /**
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function createTask(array $data): ?WorkTask
    {
        if (!$this->requestValidationHelper->validateRequiredFields($data, WorkTask::REQUIRED_FIELDS)) {
            throw new BadRequestHttpException(
                'Missing required parameters'
            );
        }

        $workTask = new WorkTask();
        $workTask->construction_site_id = $data['construction_site_id'];
        $workTask->employee_id = $data['employee_id'];
        $workTask->description = $data['description'];
        $workTask->start_date = $data['start_date'];
        $workTask->end_date = $data['end_date'];

        if (!$workTask->validate()) {
            throw new Exception('Validation failed: ' . json_encode($workTask->errors));
        }

        if (!$this->workTaskRepository->save($workTask)) {
            throw new Exception('Failed to save work task.');
        }

        return $workTask;
    }

    /**
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function updateTask(int $id, array $data): ?WorkTask
    {
        if (!$this->requestValidationHelper->validateRequiredFields($data, WorkTask::REQUIRED_FIELDS)) {
            throw new BadRequestHttpException(
                'Missing required parameters'
            );
        }

        $task = $this->workTaskRepository->find($id);
        if (!$task) {
            throw new Exception('Work task not found.');
        }

        $task->load($data, '');

        if (!$task->validate()) {
            throw new Exception('Validation failed: ' . json_encode($task->errors));
        }

        if (!$this->workTaskRepository->save($task)) {
            throw new Exception('Failed to update Work task.');
        }

        return $task;
    }

    /**
     * @throws Exception
     */
    public function deleteTask(int $id): bool
    {
        $task = $this->workTaskRepository->find($id);
        if (!$task) {
            throw new Exception('Task not found.');
        }

        if (!$this->workTaskRepository->delete($task)) {
            throw new Exception('Failed to delete task.');
        }

        return true;
    }

    /**
     * @return WorkTask[]
     */
    public function getTasksByEmployeeId(int $employeeId): array
    {
        return $this->workTaskRepository->getTasksByEmployeeId($employeeId);
    }
}