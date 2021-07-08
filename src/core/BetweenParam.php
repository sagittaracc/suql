<?php

namespace suql\core;

/**
 * Фильтрация типа BETWEEN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class BetweenParam extends Param
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return implode(' and ', $this->getPlaceholderList());
    }
}
