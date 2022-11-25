<?php

class HelloMessage extends Component
{
    public $name;

    public $count;

    public function view()
    {
        return
            '<p>' .
                'Hello, ' . $this->variable('name') .
            '</p>' .
            $this->textInput('name')
                ->bind('onkeyup', function () {
                    return $this->setState(['name' => Input::currentValue()]);
                }) .
            $this->button()
                ->setCaption('Click')
                ->bind('onclick', function () {
                    return $this->setState([
                        'count' => $this->javascript('this.count + 1'),
                        'name' => Value::string('Clicked')
                    ]);
                }) .
            $this->variable('count')
        ;
    }
}