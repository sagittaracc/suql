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
        ;
    }
}