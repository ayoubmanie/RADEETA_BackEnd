<?php

namespace Entity;

use \Lib\Entity;
use Throwable;

class Temp extends Entity
{
    protected int $id = 0;
    protected int $testId = 0;
    protected string $tempNom = '';


    const ID_INVALIDE = 1;
    const TESTID_INVALIDE = 2;
    const TEMPNOM_INVALIDE = 3;


    //manager needs

    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'testId',
            'tempNom'
        ];
    }

    //attributes for the get method in the exempleManager
    public function  searchKeys(): array
    {
        return  [
            'id',
            'testId',
            'tempNom'
        ];
    }


    //attribute for the update method in the exempleManager , WHERE id
    public function classId()
    {
        return 'id';
    }

    //attributes for the updated, they are not seted by the user
    //this method is called by the entity constructor
    public function autoUpdateAttrs(): array
    {
        return [];
    }



    //setters
    public function setId($id)
    {
        if (is_numeric($id) && !empty($id)) {
            $this->id = $id;
        } else {
            // $this->erreurs[] = self::ID_INVALIDE;
            $this->erreurs[] = "invalid";
        }
    }

    public function setTestId($testId)
    {
        if (is_numeric($testId) && !empty($testId)) {
            $this->testId = $testId;
        } else {
            // $this->erreurs['testId'] = self::TESTID_INVALIDE;
            $this->erreurs['testId'] = "invalid";
        }
    }

    public function setTempNom($tempNom)
    {
        //add password constraints
        if (is_string($tempNom) && !empty($tempNom)) {
            $this->tempNom = $tempNom;
        } else {
            // $this->erreurs['tempNom'] = self::PASSWORD_INVALIDE;
            $this->erreurs['tempNom'] = "invalid";
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

    public function tempNom()
    {
        return $this->tempNom;
    }
}