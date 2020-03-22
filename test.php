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
  users { id@uid }

  [uid <--> user_id]

  user_group {}

  [group_id <--> gid]
  
  groups { id@gid, name, name@count.group.count };
";

$suql = new SuQL($sql);

$tmp = $suql->pureSQL();

if (!$tmp)
  echo $suql->getError();
else
  dump($tmp);
