<?php

namespace suql\core;

/**
 * Обработка пользовательских плейсхолдеров
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SuQLPlaceholder
{
    /**
     * @var string $placeholder название плейсхолдера
     */
    private $placeholder;

    /**
     * Constructor
     * @param string $placeholder название плейсхолдера
     */
    function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
    }
    /**
     * Получить форматированное название плейсхолдера
     * @return string форматированное название плейсхолдера
     */
    public function getPlaceholder()
    {
        return ":$this->placeholder";
    }
}