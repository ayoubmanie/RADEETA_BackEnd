<?php

namespace Controller;

use Lib\BackController;
use Entity\HistoriqueService;

class HistoriqueServiceController extends BackController
{
    public function executeAdd()
    {
        if ($this->httpMethod == 'POST') {
            $Object = new HistoriqueService('add', $this->data, $this->app->config());
            $this->response = $this->managers->getManagerOf('historiqueService')->add($Object);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : POST.");
        }
    }

    public function executeUpdate()
    {
        if ($this->httpMethod == 'PATCH') {

            $Object = new HistoriqueService('update', $this->data, $this->app->config());
            $this->response = $this->managers->getManagerOf('historiqueService')->update($Object);
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : PATCH.");
        }
    }
}