<?php

namespace resurs\models;

use resurs\records\ResursRecord;

class Counter extends ResursRecord
{
    public function table()
    {
        return 'counter';
    }
}
