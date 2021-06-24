<?php

namespace Controller;

use Entity\RefreshToken;
use Lib\BackController;
use Lib\Authentication;
use \Entity\User;

class UserController extends BackController
{

    public function executeGet()
    {
        if ($this->HTTPMethod == 'GET') {

            $actionMethod = $this->action;
            $this->view = $this->managers->getManagerOf($this->model)->$actionMethod(($this->data)['id']);
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : GET.");
        }
    }

    public function executeAuthorization()
    {
        //no need to check for the HTTPMethode
        if ($this->authentication->needsPermission()) {

            $this->authentication->auth();

            $this->authentication->permission();
        }
    }

    public function executeLogin()
    {
        if ($this->HTTPMethod == 'POST') {
            // $testId = ($this->data)['testId'];
            // $password = ($this->data)['password'];
            $testId = 1;
            $password = "selkan";
            $user =  $this->managers->getManagerOf('user')->isAuthentificated($testId, $password);

            if ($user) {
                //create tokens;
                $RefreshTokenController = new RefreshTokenController($this->model, $this->action, $this->httpRequest(), $this->config, $this->authentication);
                $RefreshTokenController->executeGetNew($user);

                $this->cookies = $RefreshTokenController->cookies();

                // $this->view = $RefreshTokenController->view();
            } else {
                throw new \Exception("wrong user, can not authentify");
            }
        } else {
            throw new \InvalidArgumentException("wrong HTTPMethod, try : POST.");
        }
    }


    public function executeSuspendUser($id)
    {
        $newData['id'] = $id;
        $newData['suspendu'] = '1';

        $object = new User('update', $newData, $this->config);
        $this->managers->getManagerOf('user')->update($object);

        $this->view = 'user suspended';
    }
}