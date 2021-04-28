<?php

namespace RRZE\MJML;

class Request
{
    public static function json()
    {
        $content = @file_get_contents('php://input');
        return json_decode($content, true);
    }
}
