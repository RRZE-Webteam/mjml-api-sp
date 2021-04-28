<?php

namespace RRZE\MJML;

class Response
{
    public static function json(int $code = 200, array $content)
    {
        header_remove();

        http_response_code($code);

        header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');

        header('Content-Type: application/json');

        $status = [
            200 => '200 OK',
            400 => '400 Bad Request',
            500 => '500 Internal Server Error'
        ];

        header('Status: ' . $status[$code]);

        return json_encode($content);
    }
}
