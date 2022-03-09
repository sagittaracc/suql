<?php

namespace suql\core;

class SmartDate
{
    private $smartDate;
    private $type;

    protected $patternList = [
        'simple' => [
            '/yesterday/' => true,
            '/today/'     => true,
            '/tomorrow/'  => true,
        ],
        'ago' => [
            '/(\d+) days? ago/'  => true,
            '/(\d+) weeks? ago/' => true,
            '/(\d+) years? ago/' => true,
        ],
        'last' => [
            '/last (\d+) days?/'  => true,
            '/last (\d+) weeks?/' => true,
            '/last (\d+) years?/' => true,
        ],
    ];

    protected function getSmartDateType()
    {
        if (strpos($this->smartDate, ' ago') !== false)
            return 'ago';
        else if (strpos($this->smartDate, 'last ') !== false)
            return 'last';
        else
            return 'simple';
    }

    function __construct($smartDate)
    {
        $this->smartDate = $smartDate;

        $this->type = $this->getSmartDateType();
        $cases = $this->patternList[$this->type];

        foreach ($cases as $case => $sql) {
            if (preg_match($case, $this->smartDate, $matches)) {
                // $matches[1];
            }
        }
    }

    public static function create($smartDate)
    {
        return new static($smartDate);
    }
}
