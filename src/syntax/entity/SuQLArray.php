<?php

namespace suql\syntax\entity;

use suql\syntax\ArrayInterface;

abstract class SuQLArray extends SuQLTable implements ArrayInterface
{
    /**
     * @var array данные
     */
    private $data;
    /**
     * Конструктор
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }
    /**
     * Задает данные
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
        $db = $this->getDb();
        $db->getPdo()->query($this->getBuilder()->createTemporaryTable($this));
        $db->getPdo()->query($this->getBuilder()->insertIntoTable($this->table(), $this->data()));
    }
    /**
     * Получает данные
     * @return array
     */
    public function data()
    {
        return $this->data;
    }
    /**
     * @inheritdoc
     */
    public function table()
    {
        return 'temporary_' . $this->query();
    }
    /**
     * @inheritdoc
     */
    public function create()
    {
        $data = $this->data();

        if (isset($data[0])) {
            $row = $data[0];

            foreach ($row as $key => $value) {
                switch (gettype($value)) {
                    case "integer":
                        $type = "int";
                        $length = 11;
                        break;
                    case "string":
                        $type = "varchar";
                        $length = 255;
                        break;
                    default:
                        $type = "varchar";
                        $length = 255;
                }

                $this->column($key)->setType($type)->setLength($length);
            }
        }

        return $this;
    }
    /**
     * @inheritdoc
     */
    public static function all()
    {
        $instance = parent::all();
        $instance->setData($instance->data());
        return $instance;
    }
}
