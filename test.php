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
    id@uid,
    name@uname
  } ~ uid between 1 and 3 and uid <> 2

  [uid --> user_id]

  user_group {
    *,
    group_id@ug_gi
  } ~ ug_gi <> 2

  [group_id --> gid]

  groups {
    id@gid,
    name@gname
  } ~ gname = 'admin';
";
$tmp = (new SuQL($sql))->pureSQL();
?>
<pre>
<?php print_r($tmp); ?>
</pre>
