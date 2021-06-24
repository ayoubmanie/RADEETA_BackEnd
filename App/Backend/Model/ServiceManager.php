<?php

namespace Model;

use \Lib\Manager;
use \Entity\Service;

abstract class ServiceManager extends Manager
{
    abstract protected function add(Service $service);
    abstract protected function update(Service $service);

    abstract public function get($id);
    abstract public function getList(): array;
}