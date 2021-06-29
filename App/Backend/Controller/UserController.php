<?php

namespace Controller;

use Entity\RefreshToken;
use Lib\BackController;
use Lib\Authentication;
use \Entity\User;

class UserController extends BackController
{

    // public function executeGet()
    // {
    //     if ($this->httpMethod == 'GET') {

    //         $actionMethod = $this->action;
    //         $this->view = $this->managers->getManagerOf($this->model)->$actionMethod(($this->data)['id']);
    //     } else {
    //         throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
    //     }
    // }


    public function executeLogin()
    {
        if ($this->httpMethod == 'POST') {
            // $testId = ($this->data)['testId'];
            // $password = ($this->data)['password'];
            $testId = 1;
            $password = "selkan";
            $user =  $this->managers->getManagerOf('user')->isAuthentificated($testId, $password);

            if ($user) {
                //create tokens;
                $RefreshTokenController = new RefreshTokenController($this->app, $this->model, $this->action);
                $RefreshTokenController->executeGetNew($user);

                $this->cookies = $RefreshTokenController->cookies();
            } else {
                throw new \Exception("wrong user, can not authentify");
            }
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : POST.");
        }
    }


    public function executeSuspendUser($id)
    {
        $newData['id'] = $id;
        $newData['suspendu'] = '1';

        $object = new User('update', $newData, $this->app->config());
        $this->managers->getManagerOf('user')->update($object);

        $this->view = 'user suspended';
    }
}