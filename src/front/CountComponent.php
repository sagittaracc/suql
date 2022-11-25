<?php

class CountComponent extends Component
{
    public $count;

    public function view()
    {
        return 
            $this->button()
                ->setCaption('Click')
                ->bind('onclick', function () {
                    return $this->setState(['count' => $this->javascript('this.count + 1')]);
                }) .
            $this->variable('count');
    }
}