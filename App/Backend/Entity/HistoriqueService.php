<?php

namespace Entity;

use \Lib\Entity;

class HistoriqueService extends Entity
{

    //attributes
    protected int $id;
    protected int $testId;
    protected int $serviceId;
    protected string $date;

    const ID_INVALIDE = 1;
    const TESTID_INVALIDE = 2;
    const SERVICEID_INVALIDE = 3;
    const DATE_INVALIDE = 4;


    //manager needs
    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'testId',
            'serviceId',
            'date'
        ];
    }

    //attributes for the update method in the exempleManager , WHERE id1 AND id2
    public function classId()
    {
        return 'id';
    }

    //attributes for the apdated, they are not seted by the user
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
            $this->erreurs['id'] = self::ID_INVALIDE;
        }
    }

    public function setTestId($testId, $type = "newIds")
    {
        if (is_numeric($testId) && !empty($testId)) {

            $this->testId = $testId;
        } else {
            $this->erreurs[] = self::TESTID_INVALIDE;
        }
    }

    public function setServiceId($serviceId, $type = "new")
    {
        if (is_numeric($serviceId) && !empty($serviceId)) {
            $this->serviceId = $serviceId;
        } else {
            $this->erreurs[] = self::SERVICEID_INVALIDE;
        }
    }

    public function setDate($date)
    {
        if (validDate($date)) {
            $this->date = $date;
        } else {
            $this->erreurs[] = self::DATE_INVALIDE;
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

    public function serviceId()
    {
        return $this->serviceId;
    }

    public function date()
    {
        return $this->date;
    }
}