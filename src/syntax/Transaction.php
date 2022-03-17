<?php

namespace suql\syntax;

use PDO;

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
     * @param suql\syntax\SuQL|suql\syntax\Query $model
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
        $transaction->model->getDb()->getPdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $transaction->model->getDb()->getPdo()->beginTransaction();
        return $transaction;
    }
    /**
     * Фиксация транзакции
     */
    public function commit()
    {
        $this->model->getDb()->getPdo()->commit();
    }
    /**
     * Откат транзакции в случае возникновения ошибки
     */
    public function rollback()
    {
        $this->model->getDb()->getPdo()->rollback();
    }
}