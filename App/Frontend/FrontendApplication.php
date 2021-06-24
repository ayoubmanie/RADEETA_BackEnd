<?php

namespace App\Frontend;

use Lib\EntityController;

// use \Lib\Application;

// class BackendApplication extends Application
class FrontendApplication
{
    public function __construct()
    {
        // parent::__construct();

        // $this->name = 'Frontend';
    }

    public function run()
    {

        ob_start();
        require __DIR__ . '/../../Web/index.html';
        $content = ob_get_clean();
        exit($content);
    }
}