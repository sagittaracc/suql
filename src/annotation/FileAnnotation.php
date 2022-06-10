<?php

namespace suql\annotation;

/**
 * Разбор аннотаций файла
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class FileAnnotation extends Annotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*\[File\(location="(?<location>.*?)"\s*\)\]/msi';
    /**
     * @var string
     */
    public $location;
    /**
     * @inheritdoc
     */
    public function read()
    {
        $matches = parent::readBy(self::REGEX);

        if (!empty($matches)) {
            $this->location = $matches['location'];
        }

        return $this;
    }
}