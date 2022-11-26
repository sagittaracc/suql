<?php

use suql\frontend\html\component\Component;
use suql\frontend\html\view\View;

class TodoList extends Component
{
    public $todoList;

    public $todoCount;

    public $todoUndoneCount;

    function __construct($properties)
    {
        parent::__construct($properties);
        $this->todoCount = count($this->todoList);
        $this->todoUndoneCount = count(array_filter($this->todoList, function ($todo) {
            return !$todo['done'];
        }));
    }

    public function view()
    {
        return
            '<ul>'.
                $this->range(0, count($this->todoList) - 1, function ($i) {
                    return View::render(Todo::class, $this->todoList[$i]);
                }).
            '</ul>'.
            $this->variable('todoUndoneCount').' of '.$this->variable('todoCount').' tasks done'
        ;
    }
}