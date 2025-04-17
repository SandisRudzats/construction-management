<?php

declare(strict_types=1);

namespace api\modules\AccessPass\services;

use api\helpers\RequestValidationHelper;
use api\modules\AccessPass\interfaces\AccessPassRepositoryInterface;
use api\modules\AccessPass\interfaces\AccessPassServiceInterface;
use api\modules\AccessPass\models\AccessPass;
use DateTime;
use yii\base\Exception;
use yii\db\Exception as DbException;
use yii\web\BadRequestHttpException;

readonly class AccessPassService implements AccessPassServiceInterface
{
    public function __construct(
        private AccessPassRepositoryInterface $accessPassRepository,
        private RequestValidationHelper $requestValidationHelper
    ) {
    }

    /**
     * @throws Exception | DbException
     * @throws \DateMalformedStringException
     */
    public function createAccessPass(array $data): ?AccessPass
    {
        $this->validateAllFields($data);

        $accessPass = new AccessPass();

        if (isset($data['valid_to'])) {
            // forcing end of the day
            $data['valid_to'] = $this->setEndOfDay($data['valid_to']);
        }

        $accessPass->load($data, '');

        if (!$accessPass->validate()) {
            throw new Exception('Validation failed: ' . json_encode($accessPass->errors));
        }

        if (!$this->accessPassRepository->save($accessPass)) {
            throw new Exception('Failed to save access pass.');
        }

        return $accessPass;
    }

    /**
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    public function updateAccessPassFromTask(array $data): ?AccessPass
    {
        $this->validateAllFields($data);

        $employeeId = (int)$data['employee_id'];
        $constructionSiteId = (int)$data['construction_site_id'];
        $workTaskId = (int)$data['work_task_id'];
        $validFrom = $data['valid_from'];
        // forcing end of the day
        $validTo = $this->setEndOfDay($data['valid_to']);

        $accessPass = $this->accessPassRepository->findByTaskSiteAndEmployee(
            $employeeId,
            $constructionSiteId,
            $workTaskId,
        );

        // In case when the task editor has changed the assignee of task, we create a new access pass
        if (!$accessPass) {
            $accessPass = new AccessPass();
            $accessPass->construction_site_id = $constructionSiteId;
            $accessPass->employee_id = $employeeId;
            $accessPass->work_task_id = $workTaskId;
        }

        $accessPass->valid_from = $validFrom;
        $accessPass->valid_to = $validTo;

        if (!$accessPass->validate()) {
            throw new Exception('Validation failed: ' . json_encode($accessPass->errors));
        }

        if (!$this->accessPassRepository->save($accessPass)) {
            throw new Exception('Failed to save access pass.');
        }

        return $accessPass;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function validateAccess(array $data): ?AccessPass
    {
        if (
            !$this->requestValidationHelper->validateRequiredFields(
                $data,
                ['employeeId', 'constructionSiteId', 'workTaskId', 'checkDate']
            )
        ) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $checkDateTime = $data['checkDate'];
        $employeeId = (int)$data['employeeId'];
        $constructionSiteId = (int)$data['constructionSiteId'];
        $workTaskId = (int)$data['workTaskId'];

        $accessPass = $this->accessPassRepository->findPassByParamsAndDate(
            $employeeId,
            $constructionSiteId,
            $workTaskId,
            $checkDateTime
        );

        if (!$accessPass) {
            throw new BadRequestHttpException('Access pass not found.');
        }

        return $accessPass;
    }

    /**
     * @throws BadRequestHttpException
     */
    private function validateAllFields(array $data): void
    {
        if (!$this->requestValidationHelper->validateRequiredFields($data, AccessPass::REQUIRED_FIELDS)) {
            throw new BadRequestHttpException(
                'Missing required parameters: employeeId, constructionSiteId, workTaskId, and checkDate are required.'
            );
        }
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function setEndOfDay(string $date): string
    {
        $dt = new DateTime($date);
        $dt->setTime(23, 59, 59);
        return $dt->format('Y-m-d H:i:s');
    }
}