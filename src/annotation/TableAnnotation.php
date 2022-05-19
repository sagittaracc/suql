<?php

namespace suql\annotation;

/**
 * Разбор аннотаций таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class TableAnnotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*\[Table\(name="(?<table>\w+)"\)\]/msi';
    /**
     * @var string из какой модели читать аннотацию
     */
    private $modelNameToReadFrom;
    /**
     * @var string имя таблицы
     */
    public $table;
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
     * Разбор запрошенной аннотации
     * @return self
     */
    public function read()
    {
        $model = new \ReflectionClass($this->modelNameToReadFrom);
        $file = file_get_contents($model->getFileName());

        preg_match(static::REGEX, $file, $matches);

        if (!empty($matches)) {
            $this->table = $matches['table'];
        }

        return $this;
    }
}