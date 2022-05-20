<?php

namespace suql\syntax\entity;

use ReflectionMethod;
use suql\annotation\TableAnnotation;
use suql\syntax\DbObject;
use suql\syntax\Model;
use suql\syntax\QueryObject;
use suql\syntax\SuQL;
use suql\syntax\TableInterface;
use test\suql\modifiers\CustomModifier;

abstract class SuQLTable extends SuQL implements TableInterface, DbObject, QueryObject
{
    use Model;
    /**
     * @var string используемый билдер
     */
    protected static $builderClass = null;
    /**
     * @inheritdoc
     */
    public function table()
    {
        $annotation = TableAnnotation::from(get_class($this))->read();
        return $annotation->alias ? [$annotation->table => $annotation->alias] : $annotation->table;
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
