<?php

namespace suql\syntax\entity;

use PDO;
use ReflectionMethod;
use sagittaracc\ArrayHelper;
use suql\annotation\RelationAnnotation;
use suql\annotation\TableAnnotation;
use suql\syntax\DbObject;
use suql\syntax\Model;
use suql\syntax\QueryObject;
use suql\syntax\ActiveRecord;
use suql\syntax\TableInterface;
use test\suql\modifiers\CustomModifier;

abstract class SuQLTable extends ActiveRecord implements TableInterface, DbObject, QueryObject
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
            else if ($option instanceof ActiveRecord) {
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
    public function join($option, $type = 'inner', $algorithm = 'simple', $on = '')
    {
        if (is_string($option)) {
            if (class_exists($option) && is_subclass_of($option, ActiveRecord::class)) {
                $model = $option::all();
                $this->setRelations($model->table(), $model->relations());
                $this->join($model->table(), $type, $algorithm);
            }
            else {
                $table = $option;

                if ($this->currentAnnotatedModel) {
                    $annotation = RelationAnnotation::from($this->currentAnnotatedModel)->for($table)->read();
                    if ($annotation->relation) {
                        $on = $this->getBuilder()->buildJoinOn($this->currentTable, $annotation->first_field, $annotation->second_table, $annotation->second_field);
                        $this->getScheme()->rel($this->currentTable, $table, $on);
                        $this->currentAnnotatedModel = $annotation->second_model;
                    }
                }
    
                if ($algorithm === 'simple') {
                    $this->lastJoin = $this->getSelect($this->query())->addJoin($type, $table);
                }
                else if ($algorithm === 'smart') {
                    $this->getSelect($this->query())->addSmartJoin($this->currentTable, $table, $type);
                }
    
                $this->currentTable = $table;
            }
        }
        else if (is_array($option)) {
            foreach ($option as $table => $alias) break;

            $this->lastJoin = $this->getSelect($this->query())->addJoin($type, "$table@$alias");
            $this->getSelect($this->query())->getLastJoin()->setOn($on);

            $this->currentTable = $alias;
        }
        else if ($option instanceof ActiveRecord) {
            $subquery = $option;

            if ($algorithm === 'simple') {
                $this->lastJoin = $this->getSelect($this->query())->addJoin($type, $subquery->query());
            }
            else if ($algorithm === 'smart') {
                $this->getSelect($this->query())->addSmartJoin($this->currentTable, $subquery->query(), $type);
            }

            $this->extend($subquery->getQueries());
            $this->currentTable = $subquery->query();
        }

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function fetch($method)
    {
        $pdoTypes = [
            'integer' => PDO::PARAM_INT,
            'boolean' => PDO::PARAM_BOOL,
            'NULL'    => PDO::PARAM_NULL,
            'double'  => PDO::PARAM_STR,
            'string'  => PDO::PARAM_STR,
        ];

        $methodList = [
            'all' => 'fetchAll',
            'one' => 'fetch',
        ];

        $db = $this->getDb();

        if ($this->dataInitiative()) {
            $db->getPdo()->query($this->getBuilder()->createTemporaryTable($this));
            $db->getPdo()->query($this->getBuilder()->insertIntoTable($this->table(), $this->data));
        }
        else {
            $config = $db->getConfig();
            $table = $this->table();

            $tableExistsQuery = $db->getPdo()->query($this->getBuilder()->tableExistsQuery($config, $table));
            $tableExists = $tableExistsQuery && $table ? $tableExistsQuery->fetchColumn() : true;
            if (!$tableExists) {
                $this->create();
                $db->getPdo()->query($this->getBuilder()->buildModel($this));
            }
        }

        $sth = $db->getPdo()->prepare($this->getRawSql());

        foreach ($this->getParamList() as $param => $value) {
            if (isset($pdoTypes[gettype($value)])) {
                $sth->bindValue($param, $value, $pdoTypes[gettype($value)]);
            }
        }

        $sth->execute();

        $data = $sth->{$methodList[$method]}(PDO::FETCH_ASSOC);

        if ($this->index) {
            $data = ArrayHelper::group($this->index, $data);
        }

        $result = [];

        // TODO: Сериализацию необходимо проверить
        if ($this->lastRequestedModel) {
            $lastRequestedModelName = $this->lastRequestedModel;
            $lastRequestedModel = $lastRequestedModelName::getTempInstance();
            $publicProperties = $lastRequestedModel->getPublicProperties();
            if ($this->serializeResult && count($publicProperties) > 0) {
                if ($method === 'all') {
                    foreach ($data as $row) {
                        $instance = $lastRequestedModel::all();
                        foreach ($publicProperties as $property) {
                            $instance->{$property->getName()} = $row[$property->getName()];
                        }
                        $pk = $instance->getPrimaryKey();
                        if ($pk) {
                            $instance->where([$pk => $instance->$pk]);
                        }
                        $result[] = $instance;
                    }
                }
                else if ($method === 'one') {
                    $instance = $lastRequestedModel::all();
                    if ($data) {
                        foreach ($publicProperties as $property) {
                            $instance->{$property->getName()} = $data[$property->getName()];
                        }
                    }
                    $result = $instance;
                }
            }
            else {
                $result = $data;
            }
        }
        else {
            $result = $data;
        }

        if (!empty($this->postFunctions)) {
            foreach ($this->postFunctions as $function) {
                $result = $this->$function($result);
            }
        }

        return $result;
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
