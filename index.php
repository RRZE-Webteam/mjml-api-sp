<?php

namespace RRZE\MJML;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use studio24\Rotate\Rotate;
use studio24\Rotate\Delete;

const CACHE = __DIR__ . '/cache';

const LOG = __DIR__ . '/log';

if (!is_dir(CACHE)) {
    mkdir(CACHE, 0_777, true);
}

if (!is_dir(LOG)) {
    mkdir(LOG, 0_777, true);
}

if (!defined('DEFAULT_TIMEZONE')) {
    define('DEFAULT_TIMEZONE', 'UTC');
}
date_default_timezone_set(DEFAULT_TIMEZONE);

require __DIR__ . '/vendor/autoload.php';

$log = new Logger('RRZE-MJML');
$log->pushHandler(new StreamHandler(LOG . '/error.log', Logger::ERROR));

$configSample = __DIR__ . '/config-sample.php';
$config = __DIR__ . '/config.php';
if (!file_exists($config)) {
    if (!copy($configSample, $config)) {
        $log->error('Cannot config-sample.php file to config.php file.', ['config file' => $config]);
        exit;
    }
}
require $config;

$log->error('Cannot config-sample.php file to config.php file.', ['config file' => $config]);

$rotate = new Rotate(LOG . '/*.log');
$rotate->size('1MB');
$rotate->run();

$rotate = new Delete(CACHE . '/*.html');
$rotate->deleteByFileModifiedDate('1 hour');

$rotate = new Delete(CACHE . '/*.mjml');
$rotate->deleteByFileModifiedDate('1 hour');

Router::add('/', function () {
    echo 'MJML API Service Provider';
});

Router::add('/v1/render', function () {
    (new Render(NODE_BIN, MJML_BIN))->run();
}, 'post');

Router::run('/');
