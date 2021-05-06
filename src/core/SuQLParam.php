<?php

namespace suql\core;

/**
 * Абстрактный класс параметров запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class SuQLParam
{
    /**
     * @var suql\core\SuQLFieldName поле для которого применяются параметры
     */
    protected $field;
    /**
     * @var array параметры применения
     */
    protected $params;
    /**
     * Constructor
     * @param suql\core\SuQLFieldName $field поле для которого применяются параметры
     * @param array $params параметры применения
     */
    function __construct($field, $params)
    {
        $this->field = $field;
        $this->params = $params;
    }
    /**
     * Получает хэш поля
     * @return string
     */
    public function getFieldHash()
    {
        return md5($this->field->format('%t.%n'));
    }
    /**
     * Получить поле
     * @return suql\core\SuQLFieldName
     */
    public function getField()
    {
        return $this->field;
    }
    /**
     * Получить параметры поля
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    /**
     * Получить ключ параметры
     * @return string
     */
    public function getParamKey()
    {
        return "pk_{$this->getFieldHash()}";
    }
    /**
     * Полный перечень параметрам по плейсхолдерам
     * @return array
     */
    public function getParamList()
    {
        return array_combine($this->getPlaceholderList(), $this->params);
    }
    /**
     * Получить список автосгенерированных плейсхолдеров
     * @return array
     */
    public function getPlaceholderList()
    {
        $placeholderList = [];

        foreach ($this->params as $index => $param)
        {
            $placeholderList[] = $param instanceof SuQLPlaceholder ? $param->getPlaceholder() : ":ph{$index}_{$this->getFieldHash()}";
        }

        return $placeholderList;
    }
    /**
     * Проверяет если параметр не нулевой (все значения переданных параметров не нулевые)
     * Определяется для каждого класса параметра
     */
    public function isValuable()
    {
        foreach ($this->params as $param)
        {
            if (is_null($param))
            {
                return false;
            }
        }
        
        return true;
    }
}
