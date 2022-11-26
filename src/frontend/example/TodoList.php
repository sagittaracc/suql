<?php

use suql\frontend\html\component\Component;
use suql\frontend\html\view\View;

class TodoList extends Component
{
    public $todoList;

    public function view()
    {
        return
            '<ul>'.
                $this->range(0, count($this->todoList) - 1, function ($i) {
                    return View::render(Todo::class, $this->todoList[$i]);
                }).
            '</ul>'.
            $this->computed('todoDoneCount', [$this, 'getTodoDoneCount']).
            ' of '.
            $this->computed('todoCount', [$this, 'getTodoCount']).
            ' tasks done'
        ;
    }

    public function getTodoCount()
    {
        return count($this->todoList);
    }

    public function getTodoDoneCount()
    {
        return count(array_filter($this->todoList, function ($todo) {
            return $todo['done'] === true;
        }));
    }
}