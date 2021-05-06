<?php

namespace suql\core;

/**
 * Фильтрация типа IN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLInParam extends SuQLParam
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return '(' . implode(',', $this->getPlaceholderList()) . ')';
    }
}