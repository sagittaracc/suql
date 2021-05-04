<?php

namespace suql\core;

/**
 * Объект конкретной базы данных с описанием схемы и используемых моделей
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLDatabase
{
    /**
     * @var suql\core\SuQLScheme схема
     */
    private $scheme;
    /**
     * @var array используемые модели
     */
    private $models;
    /**
     * Constructor
     */
    function __construct()
    {
    }
    /**
     * Прописывает схему базы данных
     * @param suql\core\SuQLScheme $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }
    /**
     * Добавляет модель в базу данных
     * @param suql\core\SuQL $model
     */
    public function addModel($model)
    {
        $this->models[] = $model;
    }
}
