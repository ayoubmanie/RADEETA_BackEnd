<?php

namespace App\Backend;

use Lib\BackController;
use \Lib\Application;

class BackendApplication extends Application
{

    public function run()
    {
        try {
            $controllers = $this->getController();
            $views = [];
            foreach ($controllers as $model => $controller) {
                // try {
                $controller->execute();

                $this->httpResponse->setResponseCookies($controller->cookies());
                if (!empty($controller->view())) {
                    $views[$model] = $controller->view();
                }
                //to review !!!!!!
                // } catch (\Throwable $e) {

                // }
            }
            $this->httpResponse->setResponseBody($views);






            $this->httpResponse->send();
        } catch (\Throwable $e) {
            $this->httpResponse->redirect404($e);
        }
    }
}