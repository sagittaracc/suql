<?php

namespace suql\syntax\parser;

use suql\syntax\SuQLParser;
use Symfony\Component\Yaml\Yaml as YamlYaml;

class Yaml implements SuQLParser
{
    /**
     * @inheritdoc
     */
    public function parseFile($file)
    {
        return YamlYaml::parseFile($file);
    }
}