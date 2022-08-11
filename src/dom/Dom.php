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
    private $variables = [];
    /**
     * Добавляет переменную
     * @param \suql\dom\Variable $variable
     */
    public function addVariable(Variable $variable)
    {
        $this->variables[$variable->getName()] = $variable;
    }
    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
}