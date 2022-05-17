<?php

namespace suql\syntax;

use suql\syntax\field\Field;

class JsonSuQL
{
    public static function parse($file)
    {
        $content = file_get_contents($file);
        $json = json_decode($content, true);

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