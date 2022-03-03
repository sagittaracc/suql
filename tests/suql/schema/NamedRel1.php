<?php

namespace test\suql\schema;

use suql\core\NamedRel;

class NamedRel1 extends NamedRel
{
    public function leftTable()
    {
        return ['table_1' => 't1'];
    }

    public function rightTable()
    {
        return ['table_2' => 't2'];
    }

    public function on()
    {
        return 't1.id = t2.id';
    }
}