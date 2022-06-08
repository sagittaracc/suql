<?php

namespace suql\annotation;

/**
 * Разбор аннотаций параметров сервиса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class ServiceAnnotation extends Annotation
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
     * @inheritdoc
     */
    public function read()
    {
        $matches = parent::readBy(self::REGEX);

        if (!empty($matches)) {
            $this->uri = $matches['uri'];
            $this->method = $matches['method'];
        }

        return $this;
    }
}