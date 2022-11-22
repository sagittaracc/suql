<?php

namespace suql\core\param;

/**
 * Фильтрация типа IN
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class In extends Param
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
