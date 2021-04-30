<?php

namespace suql\syntax;

/**
 * Интерфейс модели таблицы
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SuQLTableInterface
{
    /**
     * Задает название таблице
     * @return string
     */
    public function table();
}
