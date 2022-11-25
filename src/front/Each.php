<?php

class Each
{
    public static function range($start, $end, $callback)
    {
        $str = '';

        for ($i = $start; $i <= $end; $i++) {
            $str .= $callback($i);
        }

        return $str;
    }
}