<?php

declare(strict_types=1);

namespace api\modules\AccessPass\interfaces;

use api\models\AccessPass;

interface AccessPassRepositoryInterface
{
    public function find(int $id): ?AccessPass;

    public function findAll(): array;

    public function findByEmployeeAndSiteAndDate(int $employeeId, int $constructionSiteId, string $date): array;

    public function save(AccessPass $accessPass): bool;

    public function delete(AccessPass $accessPass): bool;
}