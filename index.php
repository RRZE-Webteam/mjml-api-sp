<?php

namespace RRZE\MJML;

require __DIR__ . '/vendor/autoload.php';

Router::add('/', function () {
    echo 'RRZE Mjml Server';
});

Router::add('/v1', function () {
    $bin = __DIR__ . '/node_modules/.bin/mjml';
    $cacheDir = __DIR__ . '/cache';
    (new Render($bin))->run();
}, 'post');

Router::run('/');
