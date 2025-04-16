<?php

declare(strict_types=1);

namespace api\helpers;

class RequestValidationHelper
{
    public function validateRequiredFields(array $data, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}