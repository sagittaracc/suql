<?php
include 'autoload.php';

function dump($var) {
?><pre><?php print_r($var); ?></pre><?php
}

$sql = "
  users{*};
";

$suql = new SuQL($sql);

$tmp = $suql->pureSQL();

if (!$tmp)
  echo $suql->getError();
else
  dump($tmp);
