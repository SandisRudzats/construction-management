<?php
declare(strict_types=1);

namespace api\modules\ConstructionSite\interfaces;

use api\modules\ConstructionSite\models\ConstructionSite;

interface ConstructionSiteServiceInterface
{

    public function createSite(array $data): ?ConstructionSite;

    public function updateSite(int $id, array $data): ?ConstructionSite;

    public function deleteSite(int $id): bool;
}