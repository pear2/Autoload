--TEST--
Test PEAR2\Autoload initalization
--SKIPIF--
<?php if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('SKIP Requires PHP 5.4.0 or later; you have ' . PHP_VERSION);
}
--FILE--
<?php
use testDir1\FooTrait;
require __DIR__ . '/../src/PEAR2/Autoload.php';
PEAR2\Autoload::initialize(__DIR__.'/_files');

class FooTraitUser {
    use FooTrait;
}
echo FooTraitUser::sayHello();
--EXPECT--
trait testDir1\FooTrait says hi
