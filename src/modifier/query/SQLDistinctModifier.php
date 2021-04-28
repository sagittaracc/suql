<?php

namespace suql\modifier\query;

/**
 * Описание модификаторов запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
trait SQLDistinctModifier
{
    /**
     * Distinct модификатор
     * Выполняет запрос вида select dictinct <field_list> from ...
     */
    public function distinct()
    {
        $this->getQuery($this->query())->addModifier('distinct');
        return $this;
    }
}
