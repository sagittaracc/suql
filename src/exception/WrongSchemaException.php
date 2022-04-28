<?php

namespace suql\exception;

use Exception;

class WrongSchemaException extends Exception
{
    function __construct(string $schemaClass)
    {
        parent::__construct("Schema failed. $schemaClass not found!");
    }
}