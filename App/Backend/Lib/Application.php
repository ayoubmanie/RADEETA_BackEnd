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
        $model = $route['model'];
        $action = $route['action'];


        //authorization
        $this->authentication = new Authentication($this, $model, $action);
        $userController = new UserController($this, $model, $action);
        $userController->executeAuthorization();


        // On instancie le contrôleur.
        $controllerClass = "\\Controller\\" . $model . 'Controller';
        return new $controllerClass($this, $model, $action);

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