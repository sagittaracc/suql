<?php

use suql\frontend\html\component\Component;

class Todo extends Component
{
    public $id;
    public $todo;
    public $done;

    public function view()
    {
        return '<li class="task '.($this->done ? 'done' : '').'">'.$this->todo.'</li>';
    }
}