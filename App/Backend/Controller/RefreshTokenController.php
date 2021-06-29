<?php

namespace Controller;

use Entity\RefreshToken;
use Lib\BackController;
use Lib\Authentication;
use \Entity\User;

class RefreshTokenController extends BackController
{

    protected $tokens;
    protected $id;


    // public function executeGet()
    // {
    //     if ($this->httpMethod == 'GET') {

    //         $actionMethod = $this->action;
    //         $this->view = $this->managers->getManagerOf($this->model)->$actionMethod(($this->data)['id']);
    //     } else {
    //         throw new \InvalidArgumentException("wrong httpMethod, try : GET.");
    //     }
    // }


    public function executeGetNew($user)
    {
        $payload = [
            'id' => $user['id'],
            'testId' => $user['testId'],
            'password' => $user['password'],
            'role' => $user['role'],
        ];
        $this->tokens = $this->app->authentication()->createTokens($payload);

        $this->executeAdd();

        // unset($this->tokens['refreshTokenExp']);
        $this->cookies = $this->tokens;
    }


    public function executeRefresh()
    {
        if ($this->httpMethod == 'PATCH') {

            //check if refresh token exist in database with the id 
            if ($this->isValid()) {

                $payload = $this->app->authentication()->readRefreshToken();

                $this->tokens = $this->app->authentication()->createTokens($payload, 'refresh');

                $this->executeUpdate();

                $this->cookies = $this->tokens;
                // unset($this->tokens['refreshTokenExp']);
                // $this->view = $this->tokens;
            } else {

                $this->managers->getManagerOf('refreshToken')->delete($this->id());

                //no need to suspend user, remaining time for the access token is short 
                // //--------suspend user--------//
                // $payload = $this->app->authentication()->readRefreshToken();
                // $id = $payload['id'];

                // $UserController = new UserController($this->model, $this->action, $this->httpRequest(), $this->config, $this->app->authentication());
                // $UserController->executeSuspendUser($id);

                // $this->view = $UserController->view();

                throw new \Exception("an attack has been occured, need to re-login");
            }
        } else {
            throw new \InvalidArgumentException("wrong httpMethod, try : PATCH.");
        }
    }


    public function executeAdd()
    {
        //-------save token in the database-------//
        $this->data['newRT'] = $this->tokens['refreshToken']['value'];
        $this->data['expirationDate'] = date('Y-m-d H:i:s', $this->tokens['refreshToken']['expire']);

        $object = new RefreshToken('add', $this->data, $this->app->config());
        $this->managers->getManagerOf('refreshToken')->add($object);

        $refreshTokenId =  $this->managers->getManagerOf('refreshToken')->getLastIdInserted();
        // $this->app->authentication()->createtokenZZZ();


        $this->tokens['refreshTokenId'] =  [
            "name" => "refreshTokenId",
            "value" => $refreshTokenId,
            "expire" => $this->tokens['refreshToken']['expire'],
            'httpOnly' => true
        ];
    }


    public function executeUpdate()
    {
        //-------save token in the database-------//

        $this->data['id'] = $this->id();
        $this->data['newRT'] = $this->tokens['refreshToken']['value'];
        $this->data['oldRT'] = $this->app->httpRequest()->getCookie('refreshToken');

        $object = new RefreshToken('update', $this->data, $this->app->config());

        $this->managers->getManagerOf('refreshToken')->update($object);

        $this->tokens['refreshTokenId'] =  ["name" => "refreshTokenId", "value" => $this->id(), "expire" => $this->tokens['refreshToken']['expire'], 'httpOnly' => true];
    }


    protected function isValid(): bool
    {
        // print_r($this->app->authentication()->readRefreshToken());

        $refreshToken = $this->app->httpRequest()->getCookie('refreshToken');

        $isValid = $this->managers->getManagerOf('refreshToken')->check($this->id(), $refreshToken);

        return $isValid;
    }

    protected function id()
    {
        if ($this->id == NULL) {
            $this->id = $this->app->httpRequest()->getCookie('refreshTokenId');
        }
        return $this->id;
    }
}