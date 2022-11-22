<?php

namespace suql\core\where;

/**
 * 
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Greater
{
    /**
     * @var
     */
    private $value;
    /**
     * 
     * @return
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * 
     */
    public static function integer(int $value)
    {
        $instance = new static();
        $instance->value = $value;
        return $instance;
    }
}
