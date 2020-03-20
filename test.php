<?php
require('SuQL.php');
$sql = "
  #t1 = users { * };
  #t2 = t1{ id, name };
  t2{ id };
";
$tmp = (new SuQL($sql, [1]))->execute();
?>
<pre>
<?php print_r($tmp); ?>
</pre>
