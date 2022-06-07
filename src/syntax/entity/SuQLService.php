<?php

namespace suql\syntax\entity;

use suql\syntax\ServiceInterface;

abstract class SuQLService extends SuQLArray implements ServiceInterface
{
    /**
     * @var mixed данные сервиса
     */
    private $data;
    /**
     * @inheritdoc
     */
    public function table()
    {
        return 'service_' . $this->query();
    }
    /**
     * Задание данные сервиса
     * @param mixed $data данные сервиса
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    /**
     * @inheritdoc
     */
    public function data()
    {
        return $this->data;
    }
}
