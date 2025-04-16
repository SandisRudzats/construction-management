<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\services;

use api\helpers\RequestValidationHelper;
use api\modules\ConstructionSite\interfaces\ConstructionSiteRepositoryInterface;
use api\modules\ConstructionSite\interfaces\ConstructionSiteServiceInterface;
use api\modules\ConstructionSite\models\ConstructionSite;
use yii\base\Exception;
use yii\web\BadRequestHttpException;

readonly class ConstructionSiteService implements ConstructionSiteServiceInterface
{
    public function __construct(
        private ConstructionSiteRepositoryInterface $constructionSiteRepository,
        private RequestValidationHelper $requestValidationHelper
    ) {
    }

    /**
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function createSite(array $data): ?ConstructionSite
    {
        if (!$this->requestValidationHelper->validateRequiredFields($data, ConstructionSite::REQUIRED_FIELDS)) {
            throw new BadRequestHttpException(
                'Missing required parameters'
            );
        }

        $site = new ConstructionSite();
        $site->name = $data['name'];
        $site->manager_id = $data['manager_id'];
        $site->location = $data['location'];
        $site->area = $data['area'];
        $site->required_access_level = $data['required_access_level'];

        if (!$site->validate()) {
            throw new Exception('Validation failed: ' . json_encode($site->errors));
        }

        if (!$this->constructionSiteRepository->save($site)) {
            throw new Exception('Failed to save site.');
        }

        return $site;
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function updateSite(int $id, array $data): ?ConstructionSite
    {
        if (!$this->requestValidationHelper->validateRequiredFields($data, ConstructionSite::REQUIRED_FIELDS)) {
            throw new BadRequestHttpException(
                'Missing required parameters'
            );
        }

        $site = $this->constructionSiteRepository->find($id);
        if (!$site) {
            throw new Exception('Site not found.');
        }

        $site->load($data, '');
        if (!$site->validate()) {
            throw new Exception('Validation failed: ' . json_encode($site->errors));
        }

        if (!$this->constructionSiteRepository->save($site)) {
            throw new Exception('Failed to save site.');
        }

        return $site;
    }

    /**
     * @throws Exception
     */
    public function deleteSite(int $id): bool
    {
        $site = $this->constructionSiteRepository->find($id);
        if (!$site) {
            throw new Exception('Site not found.');
        }

        if (!$this->constructionSiteRepository->delete($site)) {
            throw new Exception('Failed to delete site.');
        }

        return true;
    }
}