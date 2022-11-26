<?php

use suql\frontend\html\component\Component;
use suql\frontend\html\element\Input;
use suql\frontend\html\view\View;

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
                    return $this->setState(['name' => Input::currentValue()]);
                }) .
            $this->range(0, 4, function ($i) {
                return View::render(CountComponent::class, ['count' => $i]);
            })
        ;
    }
}