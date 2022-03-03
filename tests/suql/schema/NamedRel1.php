<?php

namespace test\suql\schema;

use suql\core\NamedRel;

class NamedRel1 extends NamedRel
{
    public function leftTable()
    {
        return 'table_1';
    }

    public function rightTable()
    {
        return 'table_2';
    }

    public function on()
    {
        return 'table_1.id = table_2.id';
    }
}