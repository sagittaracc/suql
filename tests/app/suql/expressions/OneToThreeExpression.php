<?php

namespace app\suql\expressions;

use suql\core\SimpleParam;
use suql\syntax\Expression;

class OneToThreeExpression extends Expression
{
    public static function expression()
    {
        return '$1 and $2';
    }

    public static function conditions()
    {
        return [
            [SimpleParam::class, ['users', 'id'], '$ > ?', [1]],
            [SimpleParam::class, ['users', 'id'], '$ < ?', [3]],
        ];
    }
}