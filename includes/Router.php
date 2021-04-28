<?php

namespace RRZE\MJML;

class Router
{
    protected static $routes = [];

    protected static $pathNotFound = null;

    protected static $methodNotAllowed = null;

    public static function add($expression, $function, $method = 'get')
    {
        array_push(self::$routes, [
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ]);
    }

    public static function getAll(): array
    {
        return self::$routes;
    }

    public static function pathNotFound($function)
    {
        self::$pathNotFound = $function;
    }

    public static function methodNotAllowed($function)
    {
        self::$methodNotAllowed = $function;
    }

    public static function run($basepath = '', $caseSensitive = false, $trailingSlash = false, $multimatch = false)
    {
        $basepath = rtrim($basepath, '/');

        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        $path = '/';

        if (isset($parsedUrl['path'])) {
            if ($trailingSlash) {
                $path = $parsedUrl['path'];
            } else {
                if ($basepath . '/' != $parsedUrl['path']) {
                    $path = rtrim($parsedUrl['path'], '/');
                } else {
                    $path = $parsedUrl['path'];
                }
            }
        }

        $path = urldecode($path);

        $method = $_SERVER['REQUEST_METHOD'];

        $pathMatchFound = false;

        $routeMatchFound = false;

        foreach (self::$routes as $route) {
            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }

            $route['expression'] = '^' . $route['expression'];

            $route['expression'] = $route['expression'] . '$';

            if (preg_match('#' . $route['expression'] . '#' . ($caseSensitive ? '' : 'i') . 'u', $path, $matches)) {
                $pathMatchFound = true;

                foreach ((array)$route['method'] as $allowedMethod) {
                    if (strtolower($method) == strtolower($allowedMethod)) {
                        array_shift($matches);

                        if ($basepath != '' && $basepath != '/') {
                            array_shift($matches);
                        }

                        if ($returnValue = call_user_func_array($route['function'], $matches)) {
                            echo $returnValue;
                        }

                        $routeMatchFound = true;

                        break;
                    }
                }
            }

            if ($routeMatchFound && !$multimatch) {
                break;
            }
        }

        if (!$routeMatchFound) {
            if ($pathMatchFound) {
                if (self::$methodNotAllowed) {
                    call_user_func_array(self::$methodNotAllowed, [$path, $method]);
                }
            } else {
                if (self::$pathNotFound) {
                    call_user_func_array(self::$pathNotFound, [$path]);
                }
            }
        }
    }
}
