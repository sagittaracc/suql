<?php

namespace suql\core;

/**
 * Фильтрация типа BETWEEN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLBetweenParam extends SuQLParam
{
    /**
     * @{inheritdoc}
     */
    public function getPlaceholder()
    {
        return implode(' and ', $this->getPlaceholderList());
    }
}