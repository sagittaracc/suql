<?php

namespace test\suql\schema;

use suql\core\NamedRel;

class NamedRel2 extends NamedRel
{
    public function leftTable()
    {
        return 'table_2';
    }

    public function rightTable()
    {
        return ['table_3' => 't3'];
    }

    public function on()
    {
        return '`table_2`.`id` = `t3`.`id`';
    }
}