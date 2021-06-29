<?php

namespace Model;

use \Lib\Manager;
use \Entity\User;

abstract class UserManager extends Manager
{

    abstract public function add(User $user);
    abstract public function update(User $user);

    abstract public function get(User $user);
    abstract public function getList(): array;

    abstract public function isAuthentificated($testId, $password);
}