<?php

namespace suql\core\where;

/**
 * 
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Greater extends Condition
{
    public function getCondition()
    {
        return '>';
    }
}
