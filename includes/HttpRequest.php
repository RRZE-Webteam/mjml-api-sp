<?php

namespace RRZE\MJML;

class HttpRequest
{
    protected  $addHeaders = ['CONTENT_TYPE', 'CONTENT_LENGTH'];

    protected $method;

    protected $requestMethod;

    protected $protocol;

    protected $headers;

    protected $body;


    public function __construct($addHeaders = false)
    {
        $this->retrieveHeaders($addHeaders);
        $this->body = @file_get_contents('php://input');
    }

    protected function retrieveHeaders($addHeaders = false)
    {
        if ($addHeaders) {
            $this->addHeaders = array_merge($this->addHeaders, $addHeaders);
        }

        if (isset($_SERVER['HTTP_METHOD'])) {
            $this->method = $_SERVER['HTTP_METHOD'];
            unset($_SERVER['HTTP_METHOD']);
        } else {
            $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
        }
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : false;
        $this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;

        $this->headers = [];
        foreach ($_SERVER as $i => $val) {
            if (strpos($i, 'HTTP_') === 0 || in_array($i, $this->addHeaders)) {
                $name = str_replace(array('HTTP_', '_'), array('', '-'), $i);
                $this->headers[$name] = $val;
            }
        }
    }

    public function method()
    {
        return $this->method;
    }

    public function body()
    {
        return $this->body;
    }

    protected function header($name)
    {
        $name = strtoupper($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : false;
    }

    protected function headers()
    {
        return $this->headers;
    }
}
