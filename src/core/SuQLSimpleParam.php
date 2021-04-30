<?php

namespace suql\core;

/**
 * Параметр с одним значением
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLSimpleParam extends SuQLParam
{
    /**
     * Получить только первый параметр фильтрации
     * @return mixed
     */
    public function getPlaceholder()
    {
        return $this->getPlaceholderList()[0];
    }
}