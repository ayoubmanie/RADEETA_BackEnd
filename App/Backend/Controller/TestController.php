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
        if ($this->HTTPMethod == 'GET') {
            $testId = ($this->data)['id'];
            $response['test'] = $this->managers->getManagerOf('test')->get($testId);
            $response['historiqueService'] = $this->managers->getManagerOf('historiqueService')->get($testId);

            $this->view =  (new TestView($response))->viewGetHistoriqueService();
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : GET.");
        }
    }

    public function executeGetListHistoriqueService()
    {
        if ($this->HTTPMethod == 'GET') {
            $response['test'] = $this->managers->getManagerOf('test')->getList();
            $response['historiqueService'] = $this->managers->getManagerOf('historiqueService')->getList();
            $this->view =  (new TestView($response))->viewGetListHistoriqueService();
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : GET.");
        }
    }

    public function executeAddHistoriqueService()
    {
        if ($this->HTTPMethod == 'POST') {
            $object = new HistoriqueService('add', $this->data, $this->config);
            $this->response = $this->managers->getManagerOf('historiqueService')->add($object);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : POST.");
        }
    }
}