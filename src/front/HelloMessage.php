<?php

class HelloMessage extends Component
{
    public $name;

    public function view()
    {
        return
            '<p>' .
                'Hello, ' . $this->variable('name') .
            '</p>' .
            $this->textInput('name')
                ->bind('onkeyup', function () {
                    return $this->setState('name', Input::currentValue());
                })
                ->render() .
            $this->button()
                ->setCaption('Click')
                ->bind('onclick', function () {
                    return $this->setState('name', Value::string('ddd'));
                })
                ->render()
        ;
    }
}