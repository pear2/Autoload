--TEST--
Test PEAR2\Autoload initalization w/2nd path
--FILE--
<?php
chdir(__DIR__);
require '../src/PEAR2/Autoload.php';
PEAR2\Autoload::initialize(__DIR__.'/_files');
$paths = PEAR2\Autoload::getPaths();
echo sizeof($paths);
?>
--EXPECT--
2