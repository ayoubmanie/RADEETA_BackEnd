<?php

namespace Model;

use \Lib\Manager;
use \Entity\Test;

abstract class TestManager extends Manager
{

    abstract protected function add(Test $test);
    abstract protected function update(Test $test);

    abstract public function get(Test $test);
    abstract public function getList(): array;
}