<?php
require('SuQL.php');
$sql = "
  users {
    id,
    name@_name
  };
";
$tmp = (new SuQL($sql))->pureSQL();
?>
<pre>
<?php print_r($tmp); ?>
</pre>
