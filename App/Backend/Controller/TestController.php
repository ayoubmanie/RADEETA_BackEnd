<?php

namespace Controller;

use Lib\BackController;
use Entity\HistoriqueService;
use Entity\Test;
use View\TestView;

class TestController extends BackController
{
    public function executeGetHistoriqueService()
    {
        if ($this->httpMethod == 'GET') {
            $testId = ($this->data)['id'];
            $response['test'] = $this->managers->getManagerOf('test')->get($testId);
            $response['historiqueService'] = $this->managers->getManagerOf('historiqueService')->get($testId);

            $this->view =  (new TestView($response))->viewGetHistoriqueService();
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    public function executeGetListHistoriqueService()
    {
        if ($this->httpMethod == 'GET') {
            $response['test'] = $this->managers->getManagerOf('test')->getList();
            $response['historiqueService'] = $this->managers->getManagerOf('historiqueService')->getList();
            $this->view =  (new TestView($response))->viewGetListHistoriqueService();
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
        }
    }

    public function executeAddHistoriqueService()
    {
        if ($this->httpMethod == 'POST') {
            $object = new HistoriqueService('add', $this->data, $this->app->config());
            $this->response = $this->managers->getManagerOf('historiqueService')->add($object);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : POST.");
        }
    }
}