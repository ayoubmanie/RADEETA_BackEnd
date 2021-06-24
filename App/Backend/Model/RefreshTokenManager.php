<?php

namespace Model;

use \Lib\Manager;
use \Entity\RefreshToken;

abstract class RefreshTokenManager extends Manager
{

    abstract protected function add(RefreshToken $refreshToken);
    abstract protected function update(RefreshToken $refreshToken);

    abstract public function get($id);
    abstract public function getList();
    abstract public function getLastIdInserted();

    abstract public function check(int $id, string $token): bool;

    abstract public function delete($id);
}