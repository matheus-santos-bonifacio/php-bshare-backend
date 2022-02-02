<?php

namespace BShare\Webservice\Error;

use Exception;
use Throwable;

class SystemException extends Exception
{
    public function __construct(array $message = [], $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(print_r($message, true), $code, $previous);
    }
};
