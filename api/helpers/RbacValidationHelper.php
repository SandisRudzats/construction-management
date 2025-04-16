<?php

declare(strict_types=1);

namespace api\helpers;

use Yii;
use yii\web\ForbiddenHttpException;

class RbacValidationHelper
{
    /**
     * @throws ForbiddenHttpException
     */
    public function validatePermissionsOrFail(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (Yii::$app->user->can($permission)) {
                return true;
            }
        }

        throw new ForbiddenHttpException("You don't have permission to do that.");
    }
}