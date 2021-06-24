<?php

namespace Lib;


class BackController extends ApplicationComponent
{
    protected $model = '';
    protected $action = '';
    protected $httpMethod = '';
    protected $data = '';
    protected $managers = '';
    protected $view;
    protected array $cookies = [];

    public function __construct(Application $app, $model, $action)
    {

        parent::__construct($app);

        $this->model = $model;
        $this->action = $action;
        $this->httpMethod = $app->httpRequest()->method();
        $this->data = $app->httpRequest()->allPostdata();

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
        if ($this->httpMethod == 'GET') {

            $actionMethod = $this->action;
            $this->view = $this->managers->getManagerOf($this->model)->$actionMethod(($this->data)['id']);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    public function executeGetList()
    {
        if ($this->httpMethod == 'GET') {
            $actionMethod = $this->action;
            $this->view = $this->managers->getManagerOf($this->model)->$actionMethod();
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    public function executeAdd()
    {
        if ($this->httpMethod == 'POST') {
            $entity = '\\Entity\\' . $this->model;
            // print_r($this->data);
            $Object = new $entity('add', $this->data, $this->app->config());
            $this->view = $this->managers->getManagerOf($this->model)->add($Object);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : POST.");
        }
    }

    public function executeUpdate()
    {
        if ($this->httpMethod == 'PATCH') {

            $entity = '\\Entity\\' . $this->model;
            $Object = new $entity('update', $this->data, $this->app->config);
            $this->view = $this->managers->getManagerOf($this->model)->update($Object);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : PATCH.");
        }
    }

    public function view()
    {
        return $this->view;
    }


    public function cookies()
    {
        return $this->cookies;
    }
}