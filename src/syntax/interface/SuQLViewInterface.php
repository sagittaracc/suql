<?php

namespace suql\syntax;

/**
 * Интерфейс модели представления
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SuQLViewInterface
{
    /**
     * Реализует представление
     * @return suql\syntax\SuQL
     */
    public function view();
}
