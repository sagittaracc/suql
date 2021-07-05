<?php

namespace suql\syntax;

/**
 * Управление транзакциями
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Transaction
{
    /**
     * @var suql\syntax\SuQL модель учавствующая в транзакции
     */
    private $model;
    /**
     * Constructor
     * @param suql\syntax\SuQL $model
     */
    function __construct($model)
    {
        $this->model = $model;
    }
    /**
     * Старт транзакции
     */
    public static function begin($model)
    {
        $transaction = new static($model);
        $transaction->model->getDb()->beginTransaction();
        return $transaction;
    }
    /**
     * Фиксация транзакции
     */
    public function commit()
    {
        $this->model->getDb()->commit();
    }
    /**
     * Откат транзакции в случае возникновения ошибки
     */
    public function rollback()
    {
        $this->model->getDb()->rollback();
    }
}