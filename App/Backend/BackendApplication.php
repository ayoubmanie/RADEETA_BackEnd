<?php

namespace App\Backend;

use Lib\BackController;
use \Lib\Application;

class BackendApplication extends Application
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        try {
            $controller = $this->getController();
            $controller->execute();
            $this->httpResponse->setCookies($controller->cookies());
            $this->httpResponse->setResponse($controller->view());
            $this->httpResponse->send();
        } catch (\Throwable $e) {
            $this->httpResponse->redirect404($e);
        }
    }
}