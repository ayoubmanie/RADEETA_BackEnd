<?php

namespace Lib;

use Firebase\JWT\JWT;


class Authentication extends ApplicationComponent
{
    protected $jwtDecoded = null;
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

        $accessExp = $iat + getenv('ACCESS_TOKEN_TIME');
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
            // $refreshExp = $iat + 60 * 60 * 24 * 30 * 6;
            $payload["exp"] = getenv('REFRESH_TOKEN_TIME');
            $payload["nbf"] = $accessExp - getenv('REFRESH_TOKEN_TIME_BEFORE_REFRESH');
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
        if ($this->jwtDecoded == null) {
            $decoded = JWT::decode($this->app->httpRequest()->getCookie('accessToken'), getenv('ACCESS_TOKEN_SECRET'), array('HS256'));
            $this->jwtDecoded = json_decode(json_encode($decoded), true);
        } else return true;
    }

    public function permission($model)
    {

        $controller = $model . 'Controller';
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
        // If this the user doesn't have permission for one model in minimum 
        // an error will be thrown

        foreach ($this->model as $element) {

            $controller = ucfirst($element) . 'Controller';
            $method = 'execute' . ucfirst($this->action);

            if (isset($this->app->config()->get()->Backend->Authentication->noPermission->$controller)) {

                $noPermissionMethods = $this->app->config()->get()->Backend->Authentication->noPermission->$controller;

                // echo "$method : ";
                // print_r($noPermissionMethods);
                if (in_array($method, $noPermissionMethods)) {
                    continue;
                }
            }
            // echo "$element ...";
            //if the user is not authentified, this command will throw an error
            $this->auth();
            $this->permission($element);
        }
    }
}