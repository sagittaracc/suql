<?php

namespace resurs\models;

use resurs\records\ResursRecord;

class Auth extends ResursRecord
{
    public function table()
    {
        return 'auth';
    }
}
