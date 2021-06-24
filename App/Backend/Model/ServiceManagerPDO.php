<?php

namespace Model;

use \Entity\Service;
use \Lib\ManagerPDO;

class ServiceManagerPDO extends ServiceManager
{
    use ManagerPDO;


    public function get($id)
    {
        $requete = $this->dao->prepare('SELECT * FROM service WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();

        // $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Service');
        if ($service = $requete->fetch()) {
            return  $service;
        }

        return null;
    }


    public function getList(): array
    {

        $requete = $this->dao->prepare('SELECT * FROM service');

        $requete->execute();

        // $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Service');

        if ($service = $requete->fetchAll()) {
            return  $service;
        }

        return [];
    }
}