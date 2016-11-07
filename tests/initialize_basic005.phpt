--TEST--
Test PEAR2\Autoload initalization w/2nd path AND FAIL loading non-existent class.
--FILE--
<?php
chdir(__DIR__);
require '../src/PEAR2/Autoload.php';
PEAR2\Autoload::initialize(__DIR__.'/_files');
echo testDir1\UnknownClass::sayHello();
?>
--EXPECTREGEX--
^Fatal error.*
