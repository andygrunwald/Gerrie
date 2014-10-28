<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$currentDir = __DIR__ . DIRECTORY_SEPARATOR;
$vendorFilePath = $currentDir . 'vendor/autoload.php';
if (file_exists($vendorFilePath) === false) {
    $vendorFilePath = $currentDir . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php';
}

if (file_exists($vendorFilePath) === false) {
    throw new \Exception('File "autoload.php" can`t be found', 1368817443);
}

require_once $vendorFilePath;

use Symfony\Component\ClassLoader\UniversalClassLoader;

// Setup own error handler, to throw an exception on any kind of error
function GerrieErrrorHandler($code, $message, $file, $line)
{
    $message = $file . ' (Line: ' . $line . '): ' . $message . ' (' . $code . ')';
    throw new \RuntimeException($message, 1364035754);

    return false;
}

set_error_handler('GerrieErrrorHandler');

$loader = new UniversalClassLoader();
$loader->registerNamespace('Gerrie', 'src');
$loader->register();
