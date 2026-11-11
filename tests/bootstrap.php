<?php

$autoloaders = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php', // plugin/vendor when sdk is inside vendor
    __DIR__ . '/../../../../autoload.php',
];
foreach ($autoloaders as $file) {
    if (is_file($file)) {
        require $file;
        break;
    }
}

if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
    // allow syntax checks without phpunit installed
}

spl_autoload_register(function ($class) {
    $prefix = 'Puleeno\\NhanhVn\\Tests\\';
    $baseDir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
