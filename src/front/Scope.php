<?php

class Scope
{
    private $variables = [];

    private $currentVariable = null;

    public function addVariable($name)
    {
        if (!isset($this->variables[$name])) {
            $this->variables[$name] = [
                'value' => null,
                'callbackList' => [],
            ];
        }

        $this->currentVariable = $name;

        return $this;
    }

    public function setCallback($id, $callback)
    {
        $this->variables[$this->currentVariable]['callbackList'][$id] = $callback;
    }

    public function serialize()
    {
        return json_encode($this->variables);
    }
}