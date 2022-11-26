<?php

namespace suql\frontend\html\component;

class Scope
{
    private $values = [];
    private $variables = [];

    private $currentVariable = null;

    public function addVariable($name)
    {
        if (!isset($this->variables[$name])) {
            $this->values[$name] = null;
            $this->variables[$name] = [
                'callbackList' => [],
            ];
        }

        $this->currentVariable = $name;

        return $this;
    }

    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    public function setCallback($id, $callback)
    {
        $this->variables[$this->currentVariable]['callbackList'][$id] = $callback;
        return $this;
    }

    public function serialize()
    {
        $this->values['setState'] = '%function';
        $this->values['scope'] = '%scope';
        $scope = json_encode($this->values);
        $scope = str_replace('"%function"', 'component.setState', $scope);
        $scope = str_replace('"%scope"', json_encode($this->variables), $scope);
        return $scope;
    }
}