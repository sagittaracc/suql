<?php

namespace suql\core\where;

/**
 * 
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Greater extends Compare
{
    public function getCompare()
    {
        return '>';
    }
}
