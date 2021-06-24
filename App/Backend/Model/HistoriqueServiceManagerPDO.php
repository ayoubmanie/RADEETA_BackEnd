<?php

namespace Model;

use \Entity\HistoriqueService;
use \Lib\ManagerPDO;

class HistoriqueServiceManagerPDO extends HistoriqueServiceManager
{

    use ManagerPDO;

    //might be used ! (no)
    // public function checkUpdate($requete)
    // {
    // $newRequest = $this->dao->prepare("SET FOREIGN_KEY_CHECKS=0")->execute();

    // $newRequest = "SELECT serviceId FROM historiqueservice WHERE serviceId = 50";
    // $newRequest = $this->dao->prepare($newRequest);
    // $newRequest->execute();

    // $result = $newRequest->fetch();

    // if (!$result) {
    //     throw new \Exception("serviceId not existing");
    // }
    // }

    public function get($testId)
    {
        $requete = $this->dao->prepare("SELECT service.nom,hs.date
                                        FROM historiqueService as hs
                                        JOIN test ON hs.testId = test.id 
                                        JOIN service ON hs.serviceId = service.id
                                        WHERE test.id = $testId
                                        ");

        $requete->execute();

        if ($historiqueServiceList = $requete->fetchAll()) {
            return  $historiqueServiceList;
        }

        return null;
    }


    public function getList(): array
    {
        $requete = $this->dao->prepare("SELECT test.id,service.nom,hs.date
                                        FROM historiqueService as hs
                                        JOIN test ON hs.testId = test.id 
                                        JOIN service ON hs.serviceId = service.id
                                        ");

        $requete->execute();

        if ($historiqueServiceList = $requete->fetchAll()) {
            return  $historiqueServiceList;
        }

        return [];
    }
}