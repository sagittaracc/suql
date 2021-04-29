<?php

namespace suql\core;

/**
 * Условное выражение в секции case when
 */
class SuQLCaseCondition extends SuQLCondition
{
    /**
     * {@inheritdoc}
     */
    function __construct($field, $condition)
    {
        parent::__construct($field, $condition, '%n');
    }
}