<?php

namespace suql\core;

/**
 * Параметр с одним значением
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SimpleParam extends Param
{
    /**
     * @inheritdoc
     */
    public function getPlaceholder()
    {
        $placeholderList = $this->getPlaceholderList();

        return isset($placeholderList[0])
            ? $placeholderList[0]
            : '';
    }
}
