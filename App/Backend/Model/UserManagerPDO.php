<?php

namespace Model;

use \Entity\User;
use \Lib\ManagerPDO;

class UserManagerPDO extends UserManager
{

    use ManagerPDO;


    public function get($testId)
    {
        $requete = $this->dao->prepare('SELECT * FROM user WHERE testId = :testId');
        $requete->bindValue(':testId', (int) $testId, \PDO::PARAM_INT);
        $requete->execute();

        // $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Test');
        if ($test = $requete->fetch()) {
            return  $test;
        }

        return null;
    }

    public function getList(): array
    {
        $requete = $this->dao->prepare('SELECT * FROM user');

        $requete->execute();

        if ($test = $requete->fetchAll()) {
            return  $test;
        }

        return [];
    }

    public function isAuthentificated($testId, $password)
    {
        $user = $this->get($testId);
        if ($user !== null) {
            if ($testId == $user["testId"]) {
                if (password_verify($password, $user["password"])) {
                    return $user;
                }
            }
            return false;
        }
        return false;
    }
}