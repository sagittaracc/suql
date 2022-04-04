<?php

namespace test\suql\records;

use ReflectionMethod;
use suql\syntax\SuQL;
use suql\syntax\TableInterface;
use test\suql\modifiers\CustomModifier;

abstract class ActiveRecord extends SuQL implements TableInterface
{
    protected static $schemeClass = 'test\\suql\\schema\\AppScheme';
    protected static $builderClass = 'suql\\builder\\MySQLBuilder';
    /**
     * @inheritdoc
     */
    public function query()
    {
        return $this->queryName ? $this->queryName : str_replace('\\', '_', static::class);
    }
    /**
     * @inheritdoc
     */
    public function create()
    {
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function view()
    {
        return $this;
    }
    /**
     * Проверяет если это вьюха
     * @return boolean
     */
    public function isView()
    {
        return $this->viewHasBeenOverriden();
    }
    /**
     * Проверяет если вьюха была переобъявлена
     * @return boolean
     */
    private function viewHasBeenOverriden()
    {
        $reflector = new ReflectionMethod($this, 'view');
        return $reflector->getDeclaringClass()->getName() === get_class($this);
    }
    /**
     * @inheritdoc
     */
    public function relations()
    {
        return [];
    }

    protected function modifierList()
    {
        return array_merge(
            parent::modifierList(),
            [
                CustomModifier::class,
            ]
        );
    }

    public function getDb()
    {
        return null;
    }
}
