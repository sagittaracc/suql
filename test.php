<?php
require('SuQL.php');
$sql = "
  #t1 = users
  {
    id@_id,
    name@_name,
    surname@_surname
  } ~ id = ? and (name = ? or surname = ?);

  t1{*};
";
$tmp = (new SuQL($sql))->pureSQL();
?>
<pre>
<?php print_r($tmp); ?>
</pre>
