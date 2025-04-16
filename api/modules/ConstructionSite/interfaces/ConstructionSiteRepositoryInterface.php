<?php

declare(strict_types=1);

namespace api\modules\ConstructionSite\interfaces;

use api\modules\ConstructionSite\models\ConstructionSite;

interface ConstructionSiteRepositoryInterface
{
    public function find(int $id): ?ConstructionSite;

    public function findAll(): array;

    public function save(ConstructionSite $constructionSite): bool;

    public function delete(ConstructionSite $constructionSite): bool;

    /**
     * @return ConstructionSite[]
     */
    public function getSitesByIds(array $siteIds): array;

    /**
     * @param int $id
     * @return ConstructionSite[]
     */
    public function getSitesByManagerId(int $id): array;
}