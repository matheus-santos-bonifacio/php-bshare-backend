<?php

namespace BShare\Webservice\Routes;

require __DIR__ . "/autoload.php";

use BShare\Webservice\Controllers\UserController;

$requestUrl = array_slice(explode("/", $_SERVER['REQUEST_URI']), 1);
$userController = new UserController();

class Router
{
    private static function set(string $typeVerb, string $url, $callback)
    {
        $url = array_slice(explode("/", $url), 1);
        $url = array_map(function ($param, $index) {
            global $requestUrl;

            if (substr($param, 0, 1) == ':') {
                return (isset($requestUrl[$index]) ? $requestUrl[$index] : null);
            }

            return $param;
        }, $url, range(0, count($url) - 1));

        $url = implode("/", $url);

        $_SESSION["callbacks"][$typeVerb]["/$url"] = $callback;
    }

    public static function get(string $url, $callback)
    {
        self::set("GET", $url, $callback);
    }

    public static function post(string $url, $callback)
    {
        self::set("GET", $url, $callback);
    }

    public static function put(string $url, $callback)
    {
        self::set("GET", $url, $callback);
    }

    public static function delete(string $url, $callback)
    {
        self::set("GET", $url, $callback);
    }
}

Router::get("/user/:name", function () {
    echo "Hellow word";
});
