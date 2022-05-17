<?php

namespace suql\syntax;

class JsonSuQL
{
    public static function parse($file)
    {
        $content = file_get_contents($file);
        $json = json_decode($content, true);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            foreach ($data as $key => $value) {
                $instance->select([$key => $value]);
            }
        }

        return $instance;
    }
}