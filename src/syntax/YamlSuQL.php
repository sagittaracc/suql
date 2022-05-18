<?php

namespace suql\syntax;

use suql\syntax\field\Field;
use Symfony\Component\Yaml\Yaml;

/**
 * Yaml синтакс
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class YamlSuQL
{
    /**
     * Разбор yaml запроса
     * @param string $file имя файла с запросом
     * @return \suql\syntax\SuQL
     */
    public static function parse($file)
    {
        $json = Yaml::parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            self::parseData($instance, $data);
        }

        return $instance;
    }
    /**
     * Разбор значения данных в yaml запросе
     * @param \suql\syntax\SuQL $instance текущий запрос
     * @param array $data данные по ключу
     */
    private static function parseData($instance, $data)
    {
        foreach ($data as $key => $value) {
            if (class_exists($key)) {
                $tmp = $key::getTempInstance();
                $table = $tmp->table();
                $instance->join($table);
                $instance->setCurrentTable($table);

                self::parseData($instance, $value);
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