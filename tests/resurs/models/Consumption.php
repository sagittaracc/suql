<?php

namespace resurs\models;

use resurs\records\ResursRecord;

class Consumption extends ResursRecord
{
    public function table()
    {
        return 'consumption';
    }
}
