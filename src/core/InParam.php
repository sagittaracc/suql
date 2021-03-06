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
        $placeholderList = $this->getPlaceholderList();

        return !empty($placeholderList)
            ? '(' . implode(',', $placeholderList) . ')'
            : '';
    }
}
