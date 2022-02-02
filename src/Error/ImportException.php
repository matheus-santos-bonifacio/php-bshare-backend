<?php

namespace BShare\Webservice\Error;

use Exception;

class ImportException extends SystemException
{
    public function __construct($message, $code)
    {
        parent::__construct("Error!: Path to component incorrect");
    }
}
