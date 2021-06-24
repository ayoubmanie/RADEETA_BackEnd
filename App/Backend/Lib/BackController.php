<?php

namespace Lib;

use InvalidArgumentException;

class BackController
{
    protected $model = '';
    protected $action = '';
    protected $managers = '';
    protected $HTTPMethod = '';
    protected $data;
    protected $view;
    protected array $cookies = [];
    protected $config;
    protected $authentication;

    public function __construct($model, $action, $httpRequest, $config, $authentication)
    {
        $this->model = $model;
        $this->action = $action;
        $this->HTTPMethod = $httpRequest->method();
        $this->data = $httpRequest->allPostdata();
        $this->httpRequest = $httpRequest;
        $this->config = $config;
        $this->authentication = $authentication;

        $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    }

    public function execute()
    {
        $method = 'execute' . ucfirst($this->action);

        // if (!is_callable([$this, $method])) {
        //     throw new \RuntimeException('L\'action "' . $this->action . '" n\'existe pas sur le module "' . $this->model . '"');
        // }

        $this->$method();
    }

    public function executeGet()
    {
        if ($this->HTTPMethod == 'GET') {

            $actionMethod = $this->action;
            $this->view = $this->managers->getManagerOf($this->model)->$actionMethod(($this->data)['id']);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : GET.");
        }
    }

    public function executeGetList()
    {
        if ($this->HTTPMethod == 'GET') {
            $actionMethod = $this->action;
            $this->view = $this->managers->getManagerOf($this->model)->$actionMethod();
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : GET.");
        }
    }

    public function executeAdd()
    {
        if ($this->HTTPMethod == 'POST') {
            $entity = '\\Entity\\' . $this->model;
            // print_r($this->data);
            $Object = new $entity('add', $this->data, $this->config);
            $this->view = $this->managers->getManagerOf($this->model)->add($Object);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : POST.");
        }
    }

    public function executeUpdate()
    {
        if ($this->HTTPMethod == 'PATCH') {

            $entity = '\\Entity\\' . $this->model;
            $Object = new $entity('update', $this->data, $this->config);
            $this->view = $this->managers->getManagerOf($this->model)->update($Object);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : PATCH.");
        }
    }

    public function view()
    {
        return $this->view;
    }

    public function httpRequest()
    {
        return $this->httpRequest;
    }

    public function cookies()
    {
        return $this->cookies;
    }
}