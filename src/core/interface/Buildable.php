<?php

namespace suql\core;

/**
 * Запрос собирается в билдере
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface Buildable
{
    /**
     * Ссылка на функцию в билдере
     * @return string
     */
    public function getBuilderFunction();
}
