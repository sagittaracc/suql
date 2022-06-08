<?php

namespace suql\annotation;

/**
 * Разбор аннотаций
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Annotation
{
    /**
     * @var string из какой модели читать аннотацию
     */
    protected $modelNameToReadFrom;
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
     * Разбор запрошенной аннотации по регулярному выражению
     * @param string $regex
     * @return array
     */
    public function readBy($regex)
    {
        $model = new \ReflectionClass($this->modelNameToReadFrom);
        $file = file_get_contents($model->getFileName());
        preg_match($regex, $file, $matches);
        return $matches;
    }
}