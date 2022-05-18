<?php

namespace suql\syntax;

use suql\syntax\field\Field;
use Symfony\Component\Yaml\Yaml;

class YamlSuQL
{
    public static function parse($file)
    {
        $json = Yaml::parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $instance->select([
                        new Field($key, $value)
                    ]);
                }
                else {
                    $instance->select([$key => $value]);
                }
            }
        }

        return $instance;
    }
}