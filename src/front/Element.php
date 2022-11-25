<?php

class Element
{
    protected $name;

    protected $id;

    protected $eventList = [];

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function bind($event, $callback)
    {
        $this->eventList[$event] = $callback();
        return $this;
    }

    public function buildEvents()
    {
        $events = [];
        foreach ($this->eventList as $event => $callback) {
            $events[] = "$event='$callback'";
        }
        return implode(' ', $events);
    }
}