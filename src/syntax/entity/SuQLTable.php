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
    public static function all()
    {
        $instance = parent::all();

        $instance->lastRequestedModel = static::class;
        $instance->currentAnnotatedModel = static::class;
        $instance->currentTable = null;

        $instance->setScheme(static::$schemeClass);
        $instance->addSelect($instance->query());

        if ($instance instanceof TableInterface) {
            $instance->init();

            $option = $instance->table();
            if (is_string($option)) {
                $table = $option;
                $instance->getSelect($instance->query())->addFrom($table);
                $instance->currentTable = $table;
            }
            else if (is_array($option)) {
                foreach ($option as $table => $alias) break;
                $instance->getSelect($instance->query())->addFrom("$table@$alias");
                $instance->currentTable = $alias;
            }
            else if ($option instanceof SuQL) {
                $subquery = $option;
                $instance->getSelect($instance->query())->addFrom($subquery->query());
                $instance->extend($subquery->getQueries());
                $instance->currentTable = $subquery->query();
            }

            $view = $instance->view();
            if (is_string($view)) {
                $viewQuery = $instance->getBuilder()->createView($instance);
                $instance->getDb()->getPdo()->exec($viewQuery);
            }

            $instance->select($instance->fields());

            $instance->setRelations($instance->table(), $instance->relations());
        }

        return $instance;
    }
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
