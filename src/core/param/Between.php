<?php

namespace suql\core\param;

/**
 * Фильтрация типа BETWEEN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Between extends Param
{
    /**
     * {@inheritdoc}
     */
    public function getPlaceholder()
    {
        return implode(' and ', $this->getPlaceholderList());
    }
}
