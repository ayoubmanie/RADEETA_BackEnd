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



        if (!array_key_exists(1, $requestArray)) {

            throw new \Exception("no model is given");
        } elseif (in_array($requestArray[1], ['add', 'update', 'get'])) {

            $route['action'] = $requestArray[1];

            $data = $this->app->httpRequest()->allPostdata();

            if (empty($data)) throw new \Exception("no model is given for the '$requestArray[1]' action");

            foreach ($data as $key => $value) {
                $route['model'][] = $key;
            }
        } else {
            $route['model'] = $requestArray[1];
            if (!array_key_exists(2, $requestArray)) {
                throw new \Exception("no action is given");
            } else {
                $route['action'] = $requestArray[2];
            }
        }



        return $route;
    }
}