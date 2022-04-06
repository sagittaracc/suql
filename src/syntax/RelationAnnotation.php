<?php

namespace suql\syntax;

/**
 * Разбор аннотаций связей
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class RelationAnnotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*(?<relation>hasOne|hasMany|manyToMany)\(((?<second_model>[\w\\\\]+)\[)?{SECOND_TABLE}\.(?<second_field>\w+)\]?\)\s+(protected|private|public)\s+\$(?<first_field>\w+);/msi';
    /**
     * @var string из какой модели читать аннотацию
     */
    private $modelNameToReadFrom;
    /**
     * @var string для какой таблицы прочитать аннотацию
     */
    private $tableNameToReadFor;
    /**
     * @var string тип связи на который указывает аннотация
     */
    public $relation = null;
    /**
     * @var string левая модель в связи
     */
    public $first_model;
    /**
     * @var string левая таблица в связи
     */
    public $first_table;
    /**
     * @var string левое поле в связи
     */
    public $first_field;
    /**
     * @var string правая модель в связи
     */
    public $second_model;
    /**
     * @var string правая таблица в связи
     */
    public $second_table;
    /**
     * @var string правое поле в связи
     */
    public $second_field;
    /**
     * Задает из какой модели читать аннотацию
     * @param string $modelName имя класса модели
     * @return self
     */
    public static function from($modelName)
    {
        $instance = new static();
        $instance->modelNameToReadFrom = $modelName;
        return $instance;
    }
    /**
     * Задает какую таблицу искать в аннотации
     * @param string $tableName имя таблицы
     * @return self
     */
    public function for($tableName)
    {
        $this->tableNameToReadFor = $tableName;
        return $this;
    }
    /**
     * Разбор запрошенной аннотации
     * @return self
     */
    public function read()
    {
        $model = new \ReflectionClass($this->modelNameToReadFrom);
        $file = file_get_contents($model->getFileName());
        $regex = str_replace('{SECOND_TABLE}', $this->tableNameToReadFor, static::REGEX);

        preg_match($regex, $file, $matches);

        if (!empty($matches)) {
            $this->relation = $matches['relation'];
            $this->second_model = $matches['second_model'];
            $this->second_table = $this->tableNameToReadFor;
            $this->second_field = $matches['second_field'];
            $this->first_field = $matches['first_field'];
        }

        return $this;
    }
}