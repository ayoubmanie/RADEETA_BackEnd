<?php

namespace Lib;

class Router
{
    protected $httpRequest;

    public function __construct($httpRequest)
    {

        $this->httpRequest = $httpRequest;
    }

    public  function route()
    {
        $requestURI = $this->httpRequest->requestURI();
        $requestArray = explode('?', $requestURI);
        $requestArray = explode('/', $requestArray[0]);
        $requestArray = array_filter($requestArray);
        $requestArray = array_values($requestArray);

        return ["model" => $requestArray[1], "action" => $requestArray[2]];
    }
}