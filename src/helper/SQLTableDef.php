<?php
namespace Helper;

class SQLTableDef {
    public $name = null;
    public $alias = null;

    function __construct($def) {
        if (is_array($def)) {
            foreach ($def as $key => $value) break;
            $this->name = $key;
            $this->alias = $value;
        } else {
            $this->name = $def;
        }
    }
}
