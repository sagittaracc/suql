<?php

namespace suql\syntax;

use suql\syntax\field\Field;
use suql\syntax\parser\Tsml;

/**
 * SuQL синтакс
 * 
 * @author Yuriy Arutyunyan <sagittaracc@gmail.com>
 */
class SuQL
{
    /**
     * Разбор запроса
     * @param string файл с запросом
     * @param \syql\syntax\SuQLParser парсер
     * @return \suql\syntax\ActiveRecord
     */
    public static function query($file, SuQLParser $parser = null)
    {
        if (is_null($parser)) {
            $parser = new Tsml;
        }

        $json = $parser->parseFile($file);

        foreach ($json as $root => $data) {
            $instance = $root::all();

            self::parse($instance, $data, $parser);
        }

        $instance->as(pathinfo($file, PATHINFO_FILENAME));

        return $instance;
    }
    /**
     * Разбор значения данных в yaml запросе
     * @param \suql\syntax\ActiveRecord $instance текущий запрос
     * @param array $data данные по ключу
     * @param \suql\syntax\SuQLParser парсер
     */
    private static function parse(&$instance, $data, $parser)
    {
        foreach ($data as $key => $value) {
            if ($key === '!buff') {
                $instance = $instance->buff();
            }
            else if (class_exists($key)) {
                $instance = $instance->join($key);
                self::parse($instance, $value, $parser);
            }
            else if (file_exists($key)) {
                $instance->join(self::query($key, $parser));
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