<?php

namespace Lib;

use \Controller\UserController;
use DevCoder\DotEnv;

abstract class Application
{
    protected $httpRequest;
    protected $httpResponse;
    protected $authentication;
    protected $config;
    protected $router;
    protected $dotEnv = __DIR__ . '../../../.env';

    public function __construct()
    {
        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        $this->config = new Config;
        $this->router = new Router($this);
        (new DotEnv($this->dotEnv))->load();
    }

    public function getController()
    {
        //routing
        $route = $this->router->route();

        //authentication
        // $this->authentication = new Authentication($this, $model, $action);
        // $this->authentication->needsPermission();


        if ($route['action'] == "search") {


            $controllerClass = "\\Lib\BackController";


            $controller = new $controllerClass($this, "Global", $route['action']);

            return $controller;
        } else {
            $model = $route['model'];
            if (!is_array($model)) $model = [$model];
            $action = $route['action'];




            // On instancie le contrôleur.
            foreach ($model as $element) {
                $controllerClass = "\\Controller\\" . $element . 'Controller';
                $controllers[$element] = new $controllerClass($this, $element, $action);
            }
            return $controllers;
        }


        // try {
        //     return new $controllerClass($route["module"], $route["action"]);
        // } catch (\Throwable $e) {
        //     throw $e;
        //     // throw new \Throwable("le module : " . $route["module"] . " n'existe pas!");
        //     // $this->httpResponse->redirect404("le module : " . $route["module"] . " n'existe pas!");
        // }
    }

    abstract public function run();



    public function httpRequest()
    {
        return $this->httpRequest;
    }

    public function httpResponse()
    {
        return $this->httpResponse;
    }

    public function config()
    {
        return $this->config;
    }
    public function authentication()
    {
        return $this->authentication;
    }
}