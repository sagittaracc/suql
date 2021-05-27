<?php

namespace resurs\records;

use suql\syntax\SuQL;

abstract class ResursRecord extends SuQL
{
    protected static $schemeClass = 'resurs\\schema\\AppSchema';
    protected static $sqlDriver = 'mysql';
}