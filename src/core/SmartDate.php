<?php

namespace suql\core;

/**
 * Умная дата
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class SmartDate
{
    /**
     * @var string умная дата
     */
    private $date;
    /**
     * @var string числа из умной даты
     */
    private $number;
    /**
     * @var string период из умной даты
     */
    private $period;
    /**
     * @var array перечень возможных умных дат по типам
     */
    protected $patternList = [
        'simple' => [
            '/(yesterday)/' => true,
            '/(today)/'     => true,
            '/(tomorrow)/'  => true,
        ],
        'ago' => [
            '/(\d+) (day)s? ago/'  => true,
            '/(\d+) (week)s? ago/' => true,
            '/(\d+) (year)s? ago/' => true,
        ],
        'last' => [
            '/last (\d+) (day)s?/'  => true,
            '/last (\d+) (week)s?/' => true,
            '/last (\d+) (year)s?/' => true,
        ],
    ];
    /**
     * Получает тип умной даты
     * @return string
     */
    public function getType()
    {
        if (strpos($this->date, ' ago') !== false)
            return 'ago';
        else if (strpos($this->date, 'last ') !== false)
            return 'last';
        else
            return 'simple';
    }
    /**
     * Constructor
     * @param string $date умная дата
     */
    function __construct($date)
    {
        $this->date = $date;

        $type = $this->getType();

        $cases = $this->patternList[$type];
        foreach ($cases as $case => $sql) {
            if (preg_match($case, $this->date, $matches)) {
                if ($type === 'simple') {
                    $this->period = $matches[1];
                }
                else {
                    $this->number = intval($matches[1]);
                    $this->period = $matches[2];
                }
                break;
            }
        }
    }
    /**
     * Получает переданную умную дату
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * Получает номер из умной даты
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * Получает период из умной даты
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }
    /**
     * Алиас для конструктора
     * @return self
     */
    public static function create($date)
    {
        return new static($date);
    }
}
