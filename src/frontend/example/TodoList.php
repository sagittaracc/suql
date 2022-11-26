<?php

use suql\frontend\html\component\Component;
use suql\frontend\html\view\View;

class TodoList extends Component
{
    public $todoList;

    public function view()
    {
        return '
            <div class="todo-form container mx-auto mt-5 px-4 py-4">
                <h1 class="text-center">TODOLIST</h1>
                <div class="input-group mb-3">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="What needs to be done?"
                        aria-describedby="button-add">
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="button-id">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="list-group mb-3">'.
                    $this->range(0, count($this->todoList) - 1, function ($i) {
                        return View::render(Todo::class, $this->todoList[$i]);
                    }).
                '</div>
                <div class="row">
                    <div class="col text-start">
                        <div class="progress" style="height: 100%;">
                            <div
                                class="progress-bar bg-warning"
                                role="progressbar"
                                style="width: 50%"
                                aria-valuenow="50"
                                aria-valuemin="0"
                                aria-valuemax="100">
                                '.
                                $this->computed('todoDoneCount', [$this, 'getTodoDoneCount']).
                                ' of '.
                                $this->computed('todoCount', [$this, 'getTodoCount']).
                                ' tasks done
                            </div>
                        </div>
                    </div>
                    <div class="col text-end">
                        <button type="button" class="btn btn-primary">Remove checked</button>
                    </div>
                </div>
            </div>
        ';
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