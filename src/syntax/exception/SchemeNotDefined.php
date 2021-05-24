<?php

namespace suql\syntax\exception;

use Exception;

class SchemeNotDefined extends Exception
{
    protected $message = 'Scheme not defined!';
}