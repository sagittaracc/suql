<?php

namespace suql\syntax\exception;

use Exception;

class BuilderNotDefined extends Exception
{
    protected $message = 'Builder not defined!';
}