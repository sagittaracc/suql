<?php

namespace suql\dom;

/**
 * Класс пути в DOM шаблоне
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Path
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    public $format;
    /**
     * @var string
     */
    public $template;
    /**
     * @Constructor
     */
    function __construct($path, $format = 'raw', $template = null)
    {
        $this->path = $path;
        $this->format = $format;
        $this->template = $template;
    }
    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}