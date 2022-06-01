<?php

namespace suql\syntax\parser;

use sagittaracc\TSML as SagittaraccTSML;
use suql\syntax\SuQLParser;

class Tsml implements SuQLParser
{
    /**
     * @inheritdoc
     */
    public function parseFile($file)
    {
        $query = file_get_contents($file);

        if ($query) {
            return SagittaraccTSML::parse($query);
        }

        return null;
    }
}