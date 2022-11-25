<?php

abstract class Component
{
    private $scope;

    function __construct($properties)
    {
        foreach ($properties as $property => $value)
        {
            $this->$property = $value;
        }

        $this->scope = new Scope;
    }

    public function render()
    {
        $view = $this->view();

        $properties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
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
                var '.static::class.' = {
                    setState: component.setState,
                    scope: '.$this->scope->serialize().'
                }
            </script>
        ';
    }

    public function variable($name)
    {
        $id = uniqid();

        $this->scope->addVariable($name)->setCallback($id, 'function (el, value) { el.textContent = value }');

        return '<span id="'.$id.'">{{'.$name.'}}</span>';
    }

    public function textInput($name)
    {
        $id = uniqid();

        $this->scope->addVariable($name)->setCallback($id, 'function (el, value) { el.value = value }');

        return '<input type="text" value="{{'.$name.'}}" id="'.$id.'">';
    }

    abstract public function view();
}