<?php

namespace Model;

use \Lib\Manager;
use \Entity\HistoriqueService;

abstract class HistoriqueServiceManager extends Manager
{

    abstract public function add(HistoriqueService $historiqueService);
    abstract public function update(HistoriqueService $historiqueService);

    abstract public function get($testId);
    abstract public function getList(): array;
}