<?php

namespace suql\core\where;

/**
 * 
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Less extends Condition
{
    public function getCondition()
    {
        return '<';
    }
}
