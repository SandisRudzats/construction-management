<?php

declare(strict_types=1);

namespace api\helpers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class UserRequestValidationHelper
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

    /**
     * @throws BadRequestHttpException
     */
    public function ensureJsonRequest(): void
    {
        $method = Yii::$app->request->method;

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (Yii::$app->request->contentType !== 'application/json') {
                throw new BadRequestHttpException('Only JSON requests are allowed.');
            }
        }
    }

    public function sanitizeRequestData(): void
    {
        $method = Yii::$app->request->method;
        if (!in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return;
        }

        $data = Yii::$app->request->bodyParams;
        $sanitized = $this->sanitize($data);
        Yii::$app->request->setBodyParams($sanitized);
    }

    protected function sanitize(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $clean[$key] = trim(strip_tags($value));
            } elseif (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
}