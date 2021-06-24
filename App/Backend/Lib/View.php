<?php

namespace Lib;

class View
{
    protected $response;
    protected $view = [];

    public function __construct($response)
    {
        $this->response = $response;
    }
}