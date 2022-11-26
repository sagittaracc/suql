<?php

namespace suql\frontend\html\component;

use suql\frontend\html\element\Button;
use suql\frontend\html\element\Input;

abstract class Component
{
    private $scope;

    private $uid;

    function __construct($properties)
    {
        $this->uid = uniqid();

        foreach ($properties as $property => $value)
        {
            $this->$property = $value;
        }

        $this->scope = new Scope;
    }

    private function className()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return $className . $this->uid;
    }

    public function setState($obj)
    {
        $s = [];
        foreach ($obj as $prop => $value) {
            $s[] = "$prop:$value";
        }
        return $this->className().'.setState({'.implode(',', $s).'})';
    }

    public function render()
    {
        $view = $this->view();

        $properties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->name;
            $view = str_replace('{{'.$propertyName.'}}', $this->$propertyName, $view);
        }

        $js = $this->getJs();

        return $view . $js;
    }

    public function getJs()
    {
        return '
            <script>
                var '.$this->className().' = '.$this->scope->serialize().'
            </script>
        ';
    }

    public function javascript($js)
    {
        return str_replace('this', $this->className(), $js);
    }

    public function variable($name)
    {
        $id = uniqid();

        $this->scope->addVariable($name)->setValue($name, $this->$name)->setCallback($id, 'function (el, value) { el.textContent = value }');

        return '<span id="'.$id.'">{{'.$name.'}}</span>';
    }

    public function textInput($name)
    {
        $id = uniqid();

        $this->scope->addVariable($name)->setCallback($id, 'function (el, value) { el.value = value }');

        $input = new Input;
        $input->setName($name)->setId($id);

        return $input;
    }

    public function button()
    {
        return new Button;
    }

    public function range($start, $end, $callback)
    {
        $str = '';

        for ($i = $start; $i <= $end; $i++) {
            $str .= $callback($i);
        }

        return $str;
    }

    abstract public function view();
}