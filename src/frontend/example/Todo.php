<?php

use suql\frontend\html\component\Component;

class Todo extends Component
{
    public $id;
    public $todo;
    public $done;

    public function view()
    {
        return '
            <label class="list-group-item">
                <input
                    class="form-check-input me-1" '.
                    ($this->done ? 'checked' : '').
                    ' type="checkbox">&nbsp;'.$this->todo.'
                <span class="float-end opacity-25">
                    <a href="#" class="bi bi-x-lg"></a>
                </span>
            </label>
        ';
    }
}