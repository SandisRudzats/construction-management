<?php
declare(strict_types=1);

namespace api\modules\AccessPass\interfaces;

use api\modules\AccessPass\models\AccessPass;

interface AccessPassServiceInterface
{
 public function createAccessPass(array $data): ?AccessPass;
 public function updateAccessPassFromTask(array $data): ?AccessPass;
 public function validateAccess(array $data): ?AccessPass;
}