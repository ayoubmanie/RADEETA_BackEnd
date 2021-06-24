<?php

namespace Lib;

use Firebase\JWT\JWT;


class Authentication extends ApplicationComponent
{
    protected $jwtDecoded;
    protected $model;
    protected $action;

    public function __construct(Application $app, $model, $action)
    {
        parent::__construct($app);

        $this->model = $model;
        $this->action = $action;
    }


    public function createTokens(array $payload, $type = '')
    {
        //save the exp date for the refresh token (not for the login)
        if (array_key_exists('exp', $payload)) {
            $payloadExp = $payload["exp"];
        }

        //---------access token----------//
        $iat = time();
        $accessExp = $iat + 5 * 60;
        // $accessExp = $iat + 15;

        // $payload["accessIat"] = $accessIat;//when the to token was created
        // $payload["nbf"] = $exp;
        $payload["exp"] = $accessExp;

        $accessJWT = JWT::encode($payload, getenv('ACCESS_TOKEN_SECRET'), 'HS256');


        //---------refresh token----------//
        $payload["iat"] = $iat;

        if ($type == 'refresh') {
            $payload["exp"] = $payloadExp;
        } else {
            $refreshExp = $iat + 60 * 60 * 24 * 30 * 6;
            $payload["exp"] = $refreshExp;
            $payload["nbf"] = $accessExp - 60;
        }

        $refreshJWT = JWT::encode($payload, getenv('REFRESH_TOKEN_SECRET'), 'HS256');


        return  [
            'accessToken' => [
                'name' => 'accessToken',
                'value' => $accessJWT,
                'expire' => $accessExp,
                'httpOnly' => true
            ],
            'refreshToken' => [
                'name' => 'refreshToken',
                'value' => $refreshJWT,
                'expire' => $payload['exp'],
                'httpOnly' => true
            ],
            'accessTokenExpDate' => [
                'name' => 'accessTokenExpDate',
                'value' => $accessExp,
                'expire' => $accessExp,
                'httpOnly' => false
            ],
            // "payload" => $payload,
            // "extra infos" => [
            //     "accessTokenExp" => date('Y/m/d H:i:s', $accessExp),
            //     "refreshTokenExp" => date('Y/m/d H:i:s', $payload['exp']),
            // ]
        ];
    }


    public function readRefreshToken()
    {
        $decoded = JWT::decode($this->app->httpRequest()->getCookie('refreshToken'), getenv('REFRESH_TOKEN_SECRET'), array('HS256'));
        $this->jwtDecoded = json_decode(json_encode($decoded), true);
        return  $this->jwtDecoded;
    }


    public function auth()
    {
        $decoded = JWT::decode($this->app->httpRequest()->getCookie('accessToken'), getenv('ACCESS_TOKEN_SECRET'), array('HS256'));
        $this->jwtDecoded = json_decode(json_encode($decoded), true);
        return  $this->jwtDecoded;
    }

    public function permission()
    {

        $controller = $this->model . 'Controller';
        $method = 'execute' . ucfirst($this->action);

        $userRole = $this->jwtDecoded['role'];
        if (isset($this->app->config()->get()->Backend->Authentication->permission->$controller->$method->roles)) {
            $allowedRoles = $this->app->config()->get()->Backend->Authentication->permission->$controller->$method->roles;

            if (!in_array($userRole, $allowedRoles)) {
                throw new \Exception("forbidden");
            }
        }

        // exemple : UserController->executeGet is restircted for the viwer
        // the viwer cann only get his own info form the the user table 

        if (isset($this->app->config()->get()->Backend->Authentication->permission->$controller->$method->conditions->$userRole)) {
            $conditions = $this->app->config()->get()->Backend->Authentication->permission->$controller->$method->conditions->$userRole;


            $allPostData = $this->app->httpRequest()->allPostdata();
            if (array_keys_exists($conditions, $allPostData)) {

                foreach ($conditions as $condition) {
                    foreach ($allPostData as $key => $value) {
                        if ($condition == $key) {
                            if ($this->jwtDecoded[$condition] != $value) {
                                throw new \Exception("forbidden");
                            }
                        }
                    }
                }
            } else {
                throw new \Exception("postData missing");
            }
        }
    }

    public function needsPermission()
    {

        $controller = $this->model . 'Controller';
        $method = 'execute' . ucfirst($this->action);

        if (isset($this->app->config()->get()->Backend->Authentication->noPermission->$controller)) {
            $allowedMethods = $this->app->config()->get()->Backend->Authentication->noPermission->$controller;
            return !in_array($method, $allowedMethods) ? true : false;
        } else {
            return true;
        }
    }
}