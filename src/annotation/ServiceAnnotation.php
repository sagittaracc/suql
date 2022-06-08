<?php

namespace suql\annotation;

/**
 * Разбор аннотаций параметров сервиса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ServiceAnnotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*\[Request\(uri="(?<uri>.*?)"\s*,\s*method="(?<method>\w+)"\)\]/msi';
    /**
     * @var string
     */
    public $uri;
    /**
     * @var string
     */
    public $method;
    /**
     * @var string из какой модели читать аннотацию
     */
    private $modelNameToReadFrom;
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
            $this->uri = $matches['uri'];
            $this->method = $matches['method'];
        }

        return $this;
    }
}