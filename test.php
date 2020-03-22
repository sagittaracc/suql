<?php

function dump($var) {
?><pre><?php print_r($var); ?></pre><?php
}

/*
 * Примечания
 * 1. В Where clause обращения к полям должно быть по алиасам.
 *    Так как в select clause будут выполнятся некоторые функциональные
 *    преобразования столбов.
 */
require('SuQL.php');
$sql = "
  users{
    name,
    name@count.group.count.desc,
    surname@count1.group.count.asc
  } ~ name = 'yuriy';
";

$suql = new SuQL($sql);

$tmp = $suql->pureSQL();

if (!$tmp)
  echo $suql->getError();
else
  dump($tmp);
