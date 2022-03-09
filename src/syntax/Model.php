<?php

/**
 * Экспериментальное и недоделанное
 */

class Field
{
    public $type;
    public $notNull = false;

    public function getType()
    {
        $types = [
            'integer' => 'int(11)',
            'string' => 'varchar(255)',
        ];

        return $types[$this->type];
    }

    public function compile()
    {
        return '# ' . $this->getType() . ($this->notNull ? ' not null' : '');

    }
}

class Model
{
    private $name;
    private $fieldList;
    private $currentField;

    function __construct($name)
    {
        $this->name = $name;
        $this->fieldList = [];
        $this->currentField = null;
    }

    public static function create($name)
    {
        return new static($name);
    }

    public function notNull()
    {
        if (!$this->currentField) {
            return;
        }

        $this->fieldList[$this->currentField]->notNull = true;

        return $this;
    }

    public function __call($name, $arguments)
    {
        $field = new Field();
        $field->type = $arguments[0];

        $this->fieldList[$name] = $field;
        $this->currentField = $name;

        return $this;
    }

    public function __toString()
    {
        $fieldList = [];

        foreach ($this->fieldList as $name => $options) {
            $fieldList[] = str_replace('#', $name, $options->compile());
        }

        return 'create table ' . $this->name . ' (' . implode(',', $fieldList) . ')' . " collate='utf8_general_ci' engine=InnoDB;";
    }
}

$c2000dz = Model::create('c2000dz')
    ->Obj_Id_Device('integer')->notNull()
    ->Alarm('integer')->notNull()
    ->StateText('string')
    ->Obj_Id_User('integer')->notNull()
    ->Obj_Id_Home('integer');

echo $c2000dz;