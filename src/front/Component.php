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

    public function setState($obj)
    {
        $s = [];
        foreach ($obj as $prop => $value) {
            $s[] = "$prop:$value";
        }
        return static::class.'.setState({'.implode(',', $s).'})';
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
                var '.static::class.' = '.$this->scope->serialize().'
            </script>
        ';
    }

    public function javascript($js)
    {
        return str_replace('this', static::class, $js);
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

        $input = new Input;
        $input->setName($name)->setId($id);

        return $input;
    }

    public function button()
    {
        return new Button;
    }

    abstract public function view();
}