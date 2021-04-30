<?php

namespace suql\syntax;

/**
 * Интерфейс модели запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SuQLQueryInterface
{
    /**
     * Задает название запросу
     * @return string
     */
    public function query();
}
