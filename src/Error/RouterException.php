<?php

namespace BShare\Webservice\Error;

use Exception;

class RouterException extends SystemException
{
    public function __construct($message, $code)
    {
        parent::__construct(["Error!: Path router to component incorrect"]);
    }
}
