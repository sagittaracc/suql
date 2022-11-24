<?php

namespace suql\syntax\exception;

use Exception;

class ConnectionIsNotSet extends Exception
{
    protected $message = 'Connection is not set!';
}