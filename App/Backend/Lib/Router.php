<?php

namespace Lib;

class Router extends ApplicationComponent
{

    public  function route()
    {
        $requestURI = $this->app->httpRequest()->requestURI();
        $requestArray = explode('?', $requestURI);
        $requestArray = explode('/', $requestArray[0]);
        $requestArray = array_filter($requestArray);
        $requestArray = array_values($requestArray);

        return [
            'model' => $requestArray[1],
            'action' => $requestArray[2]
        ];
    }
}