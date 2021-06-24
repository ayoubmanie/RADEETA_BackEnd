<?php

namespace Lib;

class HTTPRequest
{
    // public function cookieData($key)
    // {
    //     return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    // }

    // public function cookieExists($key)
    // {
    //     return isset($_COOKIE[$key]);
    // }

    // public function getData($key)
    // {
    //     return isset($_GET[$key]) ? $_GET[$key] : null;
    // }

    // public function getExists($key)
    // {
    //     return isset($_GET[$key]);
    // }

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
        return $_COOKIE[$name];
    }

    public function allPostdata()
    {
        $json = file_get_contents('php://input');

        return json_decode($json, true);


        // return ['nom' => 'service5'];
        // return ['id' => 2];

        // return ['testId' => 3, 'serviceId' => 1, 'date' => '2010-6-30'];

        // return ['id' => '1', 'nom' => 'selkane', 'dateNaissance' => '1969-8-22'];
        // return ['nom' => 'selkan', 'prenom' => 'hafida', 'dateNaissance' => '1969-8-21'];
        // return ['id' => 3, 'dateNaissance' => '1999-6-32'];
        // return $_POST;
    }
}