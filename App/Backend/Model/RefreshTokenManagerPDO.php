<?php

namespace Model;

use \Entity\RefreshToken;
use \Lib\ManagerPDO;

class RefreshTokenManagerPDO extends RefreshTokenManager
{
    use ManagerPDO;



    public function get($id)
    {
        $requete = $this->dao->prepare('SELECT * FROM refreshtoken WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();

        if ($refreshToken = $requete->fetch()) {
            return  $refreshToken;
        }

        return null;
    }

    public function getList()
    {
        $requete = $this->dao->prepare('SELECT * FROM refreshtoken ORDER BY id DESC');
        $requete->execute();

        if ($refreshToken = $requete->fetchall()) {
            return  $refreshToken;
        }

        return null;
    }

    public function getLastIdInserted()
    {
        $id = intval($this->dao->lastInsertId());
        return $id;
    }


    public function check(int $id, string $refreshtoken): bool
    {
        $requete = $this->dao->prepare('SELECT * FROM refreshtoken WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();
        if ($refreshToken = $requete->fetch()) {

            if ($refreshToken['newRT'] == $refreshtoken) return  true;

            // An attack has been has been occured
            // A reuse of the refresh token means :
            // the token is equal to the oldRt (old refresh token)
            elseif ($refreshToken['oldRT'] == $refreshtoken) return false;

            else throw new \Exception("no existing refresh token");
        } else {

            throw new \Exception("no existing refresh token id");
        }
    }


    public function delete($id)
    {
        $requete = $this->dao->prepare(' DELETE FROM refreshtoken WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();
    }
}