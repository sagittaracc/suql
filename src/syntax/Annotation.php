<?php

namespace suql\syntax;

/**
 * Разбор аннотаций
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Annotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX_BY_MODEL = '/#\s*(?<relation>hasOne|hasMany|manyToMany)\[({SECOND_MODEL}\()?(?<second_table>\w+)\.(?<second_field>\w+)\)?\]\s+(protected|private|public)\s+\$(?<first_field>\w+);/msi';
    /**
     * @var string из какой модели читать аннотацию
     */
    private $modelNameToReadFrom;
    /**
     * @var string для какой модели прочитать аннотацию
     */
    private $modelNameToReadFor = null;
    /**
     * @var string тип связи на который указывает аннотация
     */
    public $relation;
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
     * Задает какую модель искать в аннотации
     * @param string $modelName имя класса модели
     * @return self
     */
    public function for($modelName)
    {
        $this->modelNameToReadFor = $modelName;
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
        $regex = str_replace('{SECOND_MODEL}', addslashes($this->modelNameToReadFor), static::REGEX_BY_MODEL);

        preg_match($regex, $file, $matches);

        if (!empty($matches)) {
            $this->relation = $matches['relation'];
            $this->second_model = $this->modelNameToReadFor ? $this->modelNameToReadFor : $this->modelNameToReadFrom;
            $this->second_table = $matches['second_table'];
            $this->second_field = $matches['second_field'];
            $this->first_field = $matches['first_field'];
        }

        return $this;
    }
}