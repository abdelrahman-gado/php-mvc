<?php

namespace Framework\Exceptions;

use DomainException;

class PageNotFoundException extends DomainException
{
    public function __construct(string $message = "", \Throwable $previous = null) 
    {
        parent::__construct($message, 404, $previous);
    }
}