<?php

namespace suql\annotation;

/**
 * Разбор аннотаций роутов
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class RouteAnnotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*Route\(route="{ROUTE}"\s*,\s*method="{METHOD}"\)\s+public\sfunction\s(?<function>\w+)\(/msi';
    /**
     * @var string из какой модели читать аннотацию
     */
    private $modelNameToReadFrom;
    /**
     * @var string роут
     */
    private $route;
    /**
     * @var string метод
     */
    private $method;
    /**
     * @var string название функции по аннотации
     */
    public $functionName;
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
     * Задает роут и метод
     * @param string $method
     * @param string $route
     */
    public function for($method, $route)
    {
        $this->route = str_replace('/', '\/', $route);
        $this->method = $method;
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
        $regex = str_replace(['{ROUTE}', '{METHOD}'], [$this->route, $this->method], static::REGEX);

        preg_match($regex, $file, $matches);

        if (!empty($matches)) {
            $this->function = $matches['function'];
        }

        return $this;
    }
}