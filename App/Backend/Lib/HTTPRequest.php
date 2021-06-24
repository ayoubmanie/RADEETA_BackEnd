<?php

namespace Lib;

class HTTPRequest extends ApplicationComponent
{

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function postData($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public function postExists($key)
    {
        return isset($_POST[$key]);
    }

    public function requestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getCookie($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    public function allPostdata()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
}