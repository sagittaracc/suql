<?php

namespace suql\core;

/**
 * Абстрактный класс параметров запроса
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
abstract class Param
{
    /**
     * @var suql\core\FieldName поле для которого применяются параметры
     */
    protected $field;
    /**
     * @var array параметры применения
     */
    protected $params;
    /**
     * Constructor
     * @param suql\core\FieldName $field поле для которого применяются параметры
     * @param array $params параметры применения
     */
    function __construct($field, $params = [])
    {
        $this->field = $field;
        $this->params = $params;
    }
    /**
     * Получает хэш поля
     * @param mixed $value значение параметра
     * @return string
     */
    public function getFieldHash($value = 0)
    {
        return md5($this->field->format('%t.%n') . ':' . $value);
    }
    /**
     * Получить поле
     * @return suql\core\FieldName
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

        foreach ($this->params as $index => $param) {
            $placeholderList[] = $param instanceof Placeholder ? $param->getPlaceholder() : ":ph{$index}_{$this->getFieldHash($param)}";
        }

        return $placeholderList;
    }
    /**
     * Проверяет если параметр не нулевой (все значения переданных параметров не нулевые)
     * Определяется для каждого класса параметра
     * @return boolean
     */
    public function isValuable()
    {
        foreach ($this->params as $param) {
            if (is_null($param)) {
                return false;
            }
        }

        return true;
    }
}
