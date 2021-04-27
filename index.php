<?php

namespace RRZE\MJML;

require __DIR__ . '/vendor/autoload.php';

Router::add('/', function () {
    echo 'MJML API Service Provider';
});

Router::add('/v1', function () {
    $nodeBin = '';
    $mjmlBin = __DIR__ . '/node_modules/.bin/mjml';
    (new Render($nodeBin, $mjmlBin))->run();
}, 'post');

Router::run('/');
