<?php

declare(strict_types=1);

namespace api\interfaces;

use api\modules\ConstructionSite\models\ConstructionSite;

interface ConstructionSiteRepositoryInterface
{
    public function find(int $id): ?ConstructionSite;

    public function findAll(): array;

    public function save(ConstructionSite $constructionSite): bool;

    public function delete(ConstructionSite $constructionSite): bool;
}