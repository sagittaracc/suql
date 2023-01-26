<?php

namespace suql\annotation;

use suql\annotation\attributes\Table;

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
        $attributes = $model->getAttributes();
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Table) {
                return $instance;
            }
        }

        return null;
    }
}