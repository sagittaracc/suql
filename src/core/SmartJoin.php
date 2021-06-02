<?php

namespace suql\core;

/**
 * Автоматический join
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SmartJoin
{
    /**
     * @var suql\core\Select
     */
    private $oselect = null;
    /**
     * @var array упрощенная схема без связей
     */
    private $scheme;
    /**
     * @var string начальная таблица
     */
    private $fromTable;
    /**
     * @var string конечная таблица
     */
    private $toTable;
    /**
     * @var string тип join
     */
    private $type;
    /**
     * Constructor
     * @param suql\core\Select
     * @param string $fromTable начальная таблица в цепочке
     * @param string $toTable конечная таблица в цепочке
     * @param string $type тип join
     */
    function __construct($oselect, $fromTable, $toTable, $type)
    {
        $this->oselect = $oselect;
        $this->scheme = $this->getScheme();
        $this->fromTable = $fromTable;
        $this->toTable = $toTable;
        $this->type = $type;
    }
    /**
     * Получить матрицу схемы
     * @return array
     */
    private function getScheme()
    {
        $result = [];
        $rels = $this->oselect->getOSuQL()->getScheme()->getRels();

        foreach ($rels as $table => $options) {
            $result[$table] = array_keys($options);
        }

        return $result;
    }
    /**
     * Возможно выполненный шаг
     * @param string $prev
     * @param string $curr
     * @return array
     */
    private function getPossibleSteps($prev, $curr)
    {
        $steps = [];

        foreach ($this->scheme[$curr] as $step) {
            if ($step == $prev) continue;
            $steps[] = $step;
        }

        return $steps;
    }
    /**
     * Просчёт шага
     * @param string $prev
     * @param string $curr
     * @param string $end
     * @return array|null
     */
    private function step($prev, $curr, $end)
    {
        if ($curr == $end) {
            return [$end];
        }
    
        if (!isset($this->scheme[$curr])) {
            return null;
        }

        $possibleSteps = $this->getPossibleSteps($prev, $curr);
        if (empty($possibleSteps)) {
            return null;
        }
    
        foreach ($possibleSteps as $step) {
            $path = $this->step($curr, $step, $end);
            if ($path) {
                return array_merge([$curr], $path);
            }
        }
    
        return null;
    }
    /**
     * Получить цепочку
     * @return array
     */
    public function getChain()
    {
        return $this->step(null, $this->fromTable, $this->toTable);
    }
}