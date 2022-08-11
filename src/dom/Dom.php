<?php

namespace suql\dom;

/**
 * Класс управления DOM в шаблоне
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Dom
{
    /**
     * @var array
     */
    private $variableList = [];
    /**
     * Добавляет переменную
     * @param Variable $variable
     */
    public function addVariable(Variable $variable)
    {
        $this->variableList[] = $variable;
    }
}