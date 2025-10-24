<?php

namespace Vis\Http;

use Exception;
use Throwable;

class HttpNotFoundException extends Exception
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, Request::STATUS_NOT_FOUND, $previous);
    }
}