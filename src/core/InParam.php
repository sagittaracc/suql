<?php

namespace suql\core;

/**
 * Фильтрация типа IN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class InParam extends Param
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return '(' . implode(',', $this->getPlaceholderList()) . ')';
    }
}