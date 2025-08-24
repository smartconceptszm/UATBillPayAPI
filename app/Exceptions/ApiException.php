<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        public string $customMessage = 'An error occurred',
        public int $statusCode = 500,
        public string $errorCode = 'ERR_GENERIC', // your custom error code
        $previous = null
    ) {
        parent::__construct($customMessage, $statusCode, $previous);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
