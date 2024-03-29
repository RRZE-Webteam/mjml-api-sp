<?php

/*
API Name:    MJML SP
URI:         https://github.com/RRZE-Webteam/mjml-api-sp
Description: MJML API Service Provider.
Version:     1.1.1
Author:      RRZE-Webteam
Author URI:  https://blogs.fau.de/webworking/
License:     GNU General Public License Version 3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

namespace RRZE\MJML;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use studio24\Rotate\Rotate;
use studio24\Rotate\Delete;

const CACHE_DIR = __DIR__ . '/cache';

const LOG_DIR = __DIR__ . '/log';

if (!defined('DEFAULT_TIMEZONE')) {
    define('DEFAULT_TIMEZONE', 'UTC');
}
date_default_timezone_set(DEFAULT_TIMEZONE);

if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0_777, true);
}

if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0_777, true);
}

require __DIR__ . '/vendor/autoload.php';

$log = new Logger('RRZE-MJML');
$log->pushHandler(new StreamHandler(LOG_DIR . '/error.log', Logger::ERROR));

$configSample = __DIR__ . '/config-sample.php';
$config = __DIR__ . '/config.php';
if (!file_exists($config)) {
    if (!copy($configSample, $config)) {
        $log->error('Cannot config-sample.php file to config.php file.', ['config file' => $config]);
        exit;
    }
}
require $config;

$rotate = new Rotate(LOG_DIR . '/*.log');
$rotate->size('1MB');
$rotate->run();

$rotate = new Delete(CACHE_DIR . '/*.html');
$rotate->deleteByFileModifiedDate('1 hour');

$rotate = new Delete(CACHE_DIR . '/*.mjml');
$rotate->deleteByFileModifiedDate('1 minute');

Router::add('/', function () {
    echo 'MJML API Service Provider';
});

Router::add('/v1/render', function () {
    (new Render(NODE_BIN, MJML_BIN))->run();
}, 'post');

Router::run('/');
