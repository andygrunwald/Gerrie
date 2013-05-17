<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

// Setup own error handler, to throw an exception on any kind of error
function GerrieErrrorHandler($code, $message, $file, $line) {
	$message = $file . ' (Line: ' . $line . '): ' . $message . ' (' . $code . ')';
	throw new \RuntimeException($message, 1364035754);

	return false;
}
set_error_handler('GerrieErrrorHandler');

$loader = new UniversalClassLoader();
$loader->registerNamespace('Gerrie', __DIR__);
$loader->register();

define('CONFIG_FILE', __DIR__ . '/../Config.yml');