<?php

namespace suql\core\where;

/**
 * 
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Equal extends Condition
{
    public function getCondition()
    {
        return '=';
    }
}
