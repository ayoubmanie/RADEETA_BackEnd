<?php

namespace Lib;

use \Controller\UserController;
use DevCoder\DotEnv;

abstract class Application
{
    protected $httpRequest;
    protected $httpResponse;
    protected $authorization;
    protected $config;
    protected $dotEnv;
    protected $router;

    public function __construct()
    {
        $this->httpRequest = new HTTPRequest;
        $this->httpResponse = new HTTPResponse;
        $this->config = new Config(__DIR__ . '../../../Config/config.json');
        $this->router = new Router($this->httpRequest());
        (new DotEnv(__DIR__ . '../../../.env'))->load();
    }

    public function getController()
    {
        //routing
        $route = $this->router->route();
        $model = $route["model"];
        $action = $route["action"];


        //authorization
        $this->authentication = new Authentication($this->httpRequest(), $model, $action, $this->config);
        $userController = new UserController($model, $action, $this->httpRequest(), $this->config, $this->authentication);
        $userController->executeAuthorization();


        // //Authentication checking
        // $this->authentication->isAllowed($model, $action, $this->httpRequest());

        // On instancie le contrôleur.
        $controllerClass = "\\Controller\\" . $model . 'Controller';
        return new $controllerClass($model, $action, $this->httpRequest(), $this->config, $this->authentication);

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

    // public function config()
    // {
    //     return $this->config;
    // }

    // public function user()
    // {
    //     return $this->user;
    // }
}