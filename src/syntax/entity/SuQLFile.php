<?php

namespace suql\syntax\entity;

use suql\annotation\FileAnnotation;
use suql\syntax\FileInterface;

abstract class SuQLFile extends SuQLArray implements FileInterface
{
    /**
     * @var string имя файла
     */
    protected $filename;
    /**
     * @var string содержимое
     */
    protected $content;
    /**
     * Конструктор
     */
    public function __construct()
    {
    }
    /**
     * Функция предобработки содержимого файла
     * @return mixed
     */
    protected function beforeRead($file)
    {
    }
    /**
     * Выбирает поля из перечня
     * @param array $options перечень полей для выборки
     * @return \suql\syntax\entity\SuQLArray
     */
    public static function find($options = [])
    {
        if (!is_array($options)) {
            return;
        }
        
        $data = [];
        $columns = [];

        $instance = new static();

        $annotation = FileAnnotation::from($instance)->read();
        $instance->filename = $annotation->location;
        $instance->content = file_get_contents($instance->filename);
        $beforeRead = $instance->beforeRead($instance);

        foreach ($options as $field) {
            $getMethod = 'get' . ucfirst($field);
            if (method_exists($instance, $getMethod)) {
                $columns[$field] = $instance->$getMethod($instance, $beforeRead);
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
    /**
     * Получает имя файла
     * @return string
     */
    public function getName()
    {
        return $this->filename;
    }
    /**
     * Получает содержимое файла
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
