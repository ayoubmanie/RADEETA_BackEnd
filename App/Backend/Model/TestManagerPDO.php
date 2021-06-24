<?php

namespace Model;

use \Entity\Test;
use \Lib\ManagerPDO;

class TestManagerPDO extends TestManager
{
    use ManagerPDO;


    public function get($id)
    {
        $requete = $this->dao->prepare('SELECT * FROM test WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();

        // $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Test');
        if ($test = $requete->fetch()) {
            return  $test;
        }

        return null;
    }

    public function getList(): array
    {
        $requete = $this->dao->prepare('SELECT * FROM test');

        $requete->execute();

        if ($test = $requete->fetchAll()) {
            return  $test;
        }

        return [];
    }
}