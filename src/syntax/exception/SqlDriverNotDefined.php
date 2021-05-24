<?php

namespace suql\syntax\exception;

use Exception;

class SqlDriverNotDefined extends Exception
{
    protected $message = 'Sql driver not defined!';
}