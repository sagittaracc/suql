<?php

namespace suql\annotation;

/**
 * Разбор аннотаций таблиц
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class TableAnnotation extends Annotation
{
    /**
     * @const string регулярное выражение для парсинга аннотации
     */
    const REGEX = '/#\s*\[Table\(name="(?<table>\w+)"(\s*,\s*alias="(?<alias>\w+)")?\)\]/msi';
    /**
     * @var string имя таблицы
     */
    public $table;
    /**
     * @var string алиас таблицы
     */
    public $alias;
    /**
     * @inheritdoc
     */
    public function read()
    {
        $table = parent::readBy(self::REGEX);

        if ($table !== null) {
            $this->table = $table->name;
            $this->alias = $table->alias;
        }

        return $this;
    }
}