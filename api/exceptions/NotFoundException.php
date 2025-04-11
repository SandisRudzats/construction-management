<?php

declare(strict_types=1);

namespace api\exceptions;

use yii\web\HttpException;

class NotFoundException extends HttpException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(404, $message ?: 'Resource not found.', $code, $previous);
    }
}