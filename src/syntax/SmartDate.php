<?php

class SmartDate
{
    private $smartDate;
    private $builderClass;

    protected $patternList = [
        'simple' => [
            '/yesterday/' => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW() - INTERVAL 1 DAY, '%Y-%m-%d')",
            '/today/'     => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')",
            '/tomorrow/'  => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW() + INTERVAL 1 DAY, '%Y-%m-%d')",
        ],
        'ago' => [
            '/(\d+) days? ago/'  => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW() - INTERVAL # DAY, '%Y-%m-%d')",
            '/(\d+) weeks? ago/' => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW() - INTERVAL # WEEK, '%Y-%m-%d')",
            '/(\d+) years? ago/' => "DATE_FORMAT($, '%Y-%m-%d') = DATE_FORMAT(NOW() - INTERVAL # YEAR, '%Y-%m-%d')",
        ],
        'last' => [
            '/last (\d+) days?/'  => "DATE_FORMAT($, '%Y-%m-%d') >= DATE_FORMAT(NOW() - INTERVAL # DAY, '%Y-%m-%d') AND DATE_FORMAT($, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')",
            '/last (\d+) weeks?/' => "DATE_FORMAT($, '%Y-%m-%d') >= DATE_FORMAT(NOW() - INTERVAL # WEEK, '%Y-%m-%d') AND DATE_FORMAT($, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')",
            '/last (\d+) years?/' => "DATE_FORMAT($, '%Y-%m-%d') >= DATE_FORMAT(NOW() - INTERVAL # YEAR, '%Y-%m-%d') AND DATE_FORMAT($, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')",
        ],
    ];

    function __construct($smartDate, $builderClass)
    {
        $this->smartDate = $smartDate;
        $this->builderClass = $builderClass;
    }

    public static function create($smartDate)
    {
        return new static($smartDate);
    }

    protected function getSmartDateType()
    {
        if (strpos($this->smartDate, ' ago') !== false)
            return 'ago';
        else if (strpos($this->smartDate, 'last ') !== false)
            return 'last';
        else
            return 'simple';
    }

    public function getSql()
    {
        $cases = $this->patternList[$this->getSmartDateType()];

        foreach ($cases as $case => $sql) {
            if (preg_match($case, $this->smartDate, $matches)) {
                if (!empty($matches)) {
                    $sql = str_replace('#', $matches[1], $sql);
                }

                return $sql;
            }
        }

        return false;
    }
}
