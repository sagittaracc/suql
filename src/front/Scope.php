<?php

class Scope
{
    private $variables = [];

    private $search = null;

    public function addVariable($name)
    {
        if (!isset($this->variables[$name])) {
            $this->variables[$name] = [
                'value' => null,
                'callbackList' => [],
            ];
        }
    }

    public function findVariable($name)
    {
        $this->search = $name;
        return $this;
    }

    public function setCallback($id, $callback)
    {
        $this->variables[$this->search]['callbackList'][$id] = $callback;
    }

    public function getVariables()
    {
        return $this->variables;
    }
}