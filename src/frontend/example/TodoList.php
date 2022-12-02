<?php

use suql\frontend\html\component\Component;

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
                        class="btn btn-primary">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="list-group mb-3">'.
                    $this->repeat(Todo::class, 'todoList').
                '</div>
                <div class="row">
                    <div class="col text-start">
                        <div class="progress" style="height: 100%;">
                            <div
                                role="progressbar"'.
                                $this->computedAttribute([
                                    'progressWidth' => function () {
                                        return ['style', 'width: {{value}}%', $this->getProgress()];
                                    },
                                    'progressState' => function () {
                                        $progress = $this->getProgress();
                                        $state = $progress === 100 ? 'success' : ($progress < 50 ? 'danger' : 'warning');
                                        return ['class', 'progress-bar bg-{{value}}', $state];
                                    },
                                ])
                                .'
                                aria-valuenow="33"
                                aria-valuemin="0"
                                aria-valuemax="100">'.
                                $this->computed('progressString', [$this, 'getProgressString']).
                            '</div>
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

    public function getProgress()
    {
        return round($this->getTodoDoneCount() / $this->getTodoCount() * 100);
    }

    public function getProgressString()
    {
        return $this->getTodoDoneCount() . ' of ' . $this->getTodoCount() . ' tasks done';
    }
}