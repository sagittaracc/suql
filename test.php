<?php
require('SuQL.php');
$tmp = (new SuQL('users{*,id}', []))->execute();
?>
<pre>
<?php print_r($tmp); ?>
</pre>