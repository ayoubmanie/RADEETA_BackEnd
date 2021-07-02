<?php

namespace Lib;

class Managers
{
    protected $api = null;
    protected $dao = null;
    protected $managers = [];

    public function __construct($api, $dao)
    {
        $this->api = $api;
        $this->dao = $dao;
        return $this;
    }

    public function getManagerOf($module)
    {


        if (!isset($this->managers[$module])) {


            if ($module == "Global") {
                $manager = '\\Lib\\' . $module . 'Manager' . $this->api;
            } else {
                $manager = '\\Model\\' . $module . 'Manager' . $this->api;
            }

            $this->managers[$module] = new $manager($this->dao);
        }

        return $this->managers[$module];
    }
}