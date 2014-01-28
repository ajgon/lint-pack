<?php

define('TESTS_PATH', __DIR__);
define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->add('Ajgon\\LintPack', __DIR__);
