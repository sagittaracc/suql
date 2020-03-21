<?php
/*
 * Примечания
 * 1. В Where clause обращения к полям должно быть по алиасам.
 *    Так как в select clause будут выполнятся некоторые функциональные
 *    преобразования столбов.
 */
require('SuQL.php');
$sql = "
  users {
    id@_id,
    name
  } ~ _id = 2 and (name = ?)
  [id <-- group_id]
  groups {
    id,
    group_id@gid
  };
";
$tmp = (new SuQL($sql))->pureSQL();
?>
<pre>
<?php print_r($tmp); ?>
</pre>
