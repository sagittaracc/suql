<?php

namespace suql\syntax\entity;

use suql\syntax\FileInterface;

abstract class SuQLFile extends SuQLArray implements FileInterface
{
    /**
     * Конструктор
     */
    public function __construct()
    {
    }
    /**
     * Выбирает поля из перечня
     * @param array $options перечень полей для выборки
     * @return \suql\syntax\entity\SuQLArray
     */
    public static function find($options = [])
    {
        $instance = new static();
        $data = [];
        $columns = [];

        if (is_array($options)) {
            foreach ($options as $field) {
                $getMethod = 'get' . ucfirst($field);
                if (method_exists($instance, $getMethod)) {
                    $columns[$field] = $instance->$getMethod();
                }
            }
            
            foreach ($columns as $field => $list) {
                foreach ($list as $index => $value) {
                    $data[$index][$field] = $value;
                }
            }

            static::$data = $data;
    
            return parent::all();
        }
    }
}
