--TEST--
Test PEAR2\Autoload initalization
--FILE--
<?php
chdir(__DIR__);
require '../src/PEAR2/Autoload.php';
$paths = PEAR2\Autoload::getPaths();
echo sizeof($paths);
?>
--EXPECT--
1