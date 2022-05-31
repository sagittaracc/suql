<?php

namespace suql\syntax;

use suql\syntax\field\Field;
use Symfony\Component\Yaml\Yaml as YamlYaml;

/**
 * Yaml синтакс
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class Yaml
{
    /**
     * Разбор yaml запроса
     * @param string $file имя файла с запросом
     * @return \suql\syntax\SuQL
     */
    public static function query($file)
    {
        $json = YamlYaml::parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            self::parse($instance, $data);
        }

        $instance->as(basename($file, ".yaml"));

        return $instance;
    }
    /**
     * Разбор значения данных в yaml запросе
     * @param \suql\syntax\SuQL $instance текущий запрос
     * @param array $data данные по ключу
     */
    private static function parse($instance, $data)
    {
        foreach ($data as $key => $value) {
            if (class_exists($key)) {
                $tmp = $key::getTempInstance();
                $table = $tmp->table();
                $instance->join($table);
                $instance->setCurrentTable($table);

                self::parse($instance, $value);
            }
            else if (file_exists($key)) {
                $instance->join(Yaml::query($key));
            }
            else if (is_array($value)) {
                $instance->select([
                    new Field($key, $value)
                ]);
            }
            else {
                $instance->select([$key => $value]);
            }
        }
    }
}