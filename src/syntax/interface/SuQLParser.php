<?php

namespace suql\syntax;

/**
 * Парсер SuQL
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SuQLParser
{
    /**
     * @param string $file имя файла запроса
     * @return array
     */
    public function parseFile($file);
}