<?php

namespace suql\syntax;

use suql\syntax\field\Field;

/**
 * SuQL синтакс
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class SuQL1
{
    /**
     * Разбор запроса
     * @param string файл с запросом
     * @param string используемый парсер
     * @return \suql\syntax\SuQL
     */
    public static function query($file, $parser)
    {
        $json = $parser::parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            self::parse($instance, $data, $parser);
        }

        $instance->as(basename($file, ".yaml"));

        return $instance;
    }
    /**
     * Разбор значения данных в yaml запросе
     * @param \suql\syntax\SuQL $instance текущий запрос
     * @param array $data данные по ключу
     * @param string парсер
     */
    private static function parse($instance, $data, $parser)
    {
        foreach ($data as $key => $value) {
            if (class_exists($key)) {
                $tmp = $key::getTempInstance();
                $table = $tmp->table();
                $instance->join($table);
                $instance->setCurrentTable($table);

                self::parse($instance, $value, $parser);
            }
            else if (file_exists($key)) {
                $instance->join(SuQL1::query($key, $parser));
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