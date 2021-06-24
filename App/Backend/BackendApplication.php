<?php

namespace App\Backend;

use Lib\BackController;
use \Lib\Application;

class BackendApplication extends Application
{

    public function run()
    {
        try {
            $controller = $this->getController();
            $controller->execute();

            $this->httpResponse->setResponseCookies($controller->cookies());
            $this->httpResponse->setResponseBody($controller->view());

            $this->httpResponse->send();
        } catch (\Throwable $e) {
            $this->httpResponse->redirect404($e);
        }
    }
}