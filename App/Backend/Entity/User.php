<?php

namespace Entity;

use \Lib\Entity;
use Throwable;

class User extends Entity
{
    protected int $id;
    protected int $testId;
    protected string $password;
    protected string $role;
    protected string $dateModif; //datemodif is only mentionned here to let the dev know that this this table has this column
    protected int $suspendu;

    const ID_INVALIDE = 1;
    const TESTID_INVALIDE = 2;
    const ROLE_INVALIDE = 3;
    const PASSWORD_INVALIDE = 4;
    const SUSPENDU_INVALIDE = 5;


    //manager needs

    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'testId',
            'password',
            'role',
            'suspendu'
        ];
    }


    //attribute for the update method in the exempleManager , WHERE id
    public function classId()
    {
        return 'id';
    }

    //attributes for the apdated, they are not seted by the user
    public function autoUpdateAttrs(): array
    {
        return [
            'dateModif',
        ];
    }



    //setters
    public function setId($id)
    {
        if (is_numeric($id) && !empty($id)) {
            $this->id = $id;
        } else {
            $this->erreurs[] = self::ID_INVALIDE;
        }
    }

    public function setTestId($testId)
    {
        if (is_numeric($testId) && !empty($testId)) {
            $this->testId = $testId;
        } else {
            $this->erreurs['testId'] = self::TESTID_INVALIDE;
        }
    }

    public function setPassword($password)
    {
        //add password constraints
        if (is_string($password) && !empty($password)) {

            $this->password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $this->erreurs['password'] = self::PASSWORD_INVALIDE;
        }
    }

    public function setRole($role)
    {
        $config = $this->config->get();
        $roles = $config->Backend->Entity->User->role;
        if (is_string($role) && !empty($role) && in_array($role, $roles)) {

            $this->role = $role;
        } else {

            $this->erreurs['role'] = self::ROLE_INVALIDE;
        }
    }

    public function setDateModif($dateModif)
    {
        $this->dateModif = date("Y-m-d h:i:s");
    }

    public function setSuspendu($suspendu)
    {
        // var_dump($suspendu == 0 || $suspendu == 1);
        if (is_numeric($suspendu) && ($suspendu == 0 || $suspendu == 1)) {
            $this->suspendu = $suspendu;
        } else {
            $this->erreurs['suspendu'] = self::SUSPENDU_INVALIDE;
        }
    }


    //getters
    public function id()
    {
        return $this->id;
    }

    public function testId()
    {
        return $this->testId;
    }

    public function password()
    {
        return $this->password;
    }

    public function role()
    {
        return $this->role;
    }

    public function dateModif()
    {
        return $this->dateModif;
    }

    public function suspendu()
    {
        return $this->suspendu;
    }
}