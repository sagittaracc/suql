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
     * Получить только первый параметр фильтрации
     * @return mixed
     */
    public function getPlaceholder()
    {
        return $this->getPlaceholderList()[0];
    }
}