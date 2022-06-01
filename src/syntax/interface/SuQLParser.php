<?php

namespace suql\syntax;

/**
 * Парсер SuQL
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
interface SuQLParser
{
    public function parseFile($file);
}