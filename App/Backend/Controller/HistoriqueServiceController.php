<?php

namespace Controller;

use Lib\BackController;
use Entity\HistoriqueService;

class HistoriqueServiceController extends BackController
{
    public function executeAdd()
    {
        if ($this->HTTPMethod == 'POST') {
            $Object = new HistoriqueService('add', $this->data, $this->config);
            $this->response = $this->managers->getManagerOf('historiqueService')->add($Object);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : POST.");
        }
    }

    public function executeUpdate()
    {
        if ($this->HTTPMethod == 'PATCH') {

            $Object = new HistoriqueService('update', $this->data, $this->config);
            $this->response = $this->managers->getManagerOf('historiqueService')->update($Object);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : PATCH.");
        }
    }
}