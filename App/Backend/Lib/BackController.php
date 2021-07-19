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

        // Solution for the get methods with one data block (one table)
        // not many tables
        $tempData = $app->httpRequest()->allPostdata();

        if ($action == "search") {
            if (!is_array($tempData)) $tempData = [$tempData];

            $this->data = $tempData;
        } else {


            if (is_array($tempData) && array_key_exists($this->model, $tempData)) {
                $this->data = $tempData[$this->model];
            } else {
                //to review !!!!
                $this->data = $app->httpRequest()->allPostdata();
            }
        }

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

            $entity = '\\Entity\\' . $this->model;

            if (!empty($this->data)) {

                //check if data is an array or not
                // $this->data = formatJsonToArray($this->data);

                foreach ($this->data as $entitynumber => $value) {

                    $objects[$entitynumber] = new $entity('get', $value, $this->app->config());
                }


                $this->managers->getManagerOf($this->model)->get($objects);


                // exit('test');
                $this->view = $this->managers->getManagerOf($this->model)->response();

                // print_r($this->view);
                // exit;
            } else {
                throw new \InvalidArgumentException("empty post data, try : GET.");
            }
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    public function executeSearch()
    {
        if ($this->httpMethod == 'GET') {




            if (!empty($this->data)) {

                foreach ($this->data as $searchKey => $search) {

                    foreach ($search as $modelType => $values) {

                        if ($modelType == 'where') {

                            global $index;
                            global $logicOperator;
                            global $entities;


                            $index = 1;
                            $logicOperator = [];
                            $entities = [];
                            $dataTree = $this->extract_data_temp($values);


                            foreach ($entities as $model => $modelValues) {

                                $entity = '\\Entity\\' . $model;
                                $objects[$searchKey][$modelType][$model] =  new $entity('search', $modelValues, $this->app->config(), $modelType);
                            }
                        } elseif ($modelType == 'select') {
                            foreach ($values as $model => $columns) {

                                $entity = '\\Entity\\' . $model;
                                $objects[$searchKey][$modelType][$model] =  new $entity('search', $columns, $this->app->config(), $modelType);
                            }
                        }
                    }
                }





                $this->managers->getManagerOf($this->model)->search(["dataTree" => $dataTree, "logicOperator" => $logicOperator, "objects" => $objects]);
                $this->view = $this->managers->getManagerOf($this->model)->response();
            } else {
                throw new \InvalidArgumentException("empty post data, try : GET.");
            }
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    protected function extract_data_temp($data, $dataTree = [])
    {

        // print_r($dataTree);
        global $index;
        global $logicOperator;
        global $entities;

        foreach ($data as $key => $value) {


            if (in_array($value, ["and", "or"])) {

                $dataTree[$key] = $index;
                $logicOperator[$index] = $value;
                // echo "$index ";
            } elseif (is_array($value)) {

                if (jsonIsArray($value)) { // $value is an array

                    // recall

                    $dataTree[$key] = $this->extract_data_temp($value, []);
                } else { //$value is an object

                    $dataTree[$key] = $index;
                    $value["index"] = $index;

                    // $value["table"] = ucfirst($value["table"]);

                    $table = $value["table"];
                    $entities[$table][] = $value;
                }
            }
            $index++;
        }
        return $dataTree;
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

            if (!empty($this->data)) {

                //check if data is an array or not
                $this->data = formatJsonToArray($this->data);
                foreach ($this->data as $value) {
                    $objects[] = new $entity('add', $value, $this->app->config());
                }

                // print_r($objects);
                // exit;

                $this->managers->getManagerOf($this->model)->add($objects);
                // exit('test');
                $this->view = $this->managers->getManagerOf($this->model)->response();
            } else {
                throw new \InvalidArgumentException("empty post data, try : POST.");
            }
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : POST.");
        }
    }


    public function executeUpdate()
    {
        if ($this->httpMethod == 'PATCH') {

            $entity = '\\Entity\\' . $this->model;
            if (!empty($this->data)) {

                //check if data is an array or not
                $this->data = formatJsonToArray($this->data);

                foreach ($this->data as $value) {
                    $objects[] = new $entity('update', $value, $this->app->config());
                }

                $this->managers->getManagerOf($this->model)->update($objects);
                $this->view = $this->managers->getManagerOf($this->model)->response();
            } else {
                throw new \InvalidArgumentException("empty post data, try : POST.");
            }
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