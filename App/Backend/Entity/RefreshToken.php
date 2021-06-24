<?php

namespace Entity;

use \Lib\Entity;
use Throwable;

class refreshToken extends Entity
{
    protected int $id;
    protected string $newRT;
    protected string $oldRT;
    protected string $expirationDate;

    const ID_INVALIDE = 1;
    const NEWRT_INVALIDE = 2;
    const OLDRT_INVALIDE = 3;
    const EXPIRATIONDATE_INVALIDE = 4;


    //manager needs

    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'newRT',
            'expirationDate'
        ];
    }


    //attribute for the update method in the exempleManager , WHERE id
    public function classId()
    {
        return  'id';
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
            $this->erreurs[] = self::ID_INVALIDE;
        }
    }

    public function setNewRT($newRT)
    {
        if (is_string($newRT) && !empty($newRT)) {
            $this->newRT = $newRT;
        } else {
            $this->erreurs['newRT'] = self::NEWRT_INVALIDE;
        }
    }

    public function setOldRT($oldRT)
    {

        if (is_string($oldRT) && !empty($oldRT)) {
            $this->oldRT = $oldRT;
        } else {
            $this->erreurs['oldRT'] = self::OLDRT_INVALIDE;
        }
    }

    public function setExpirationDate($expirationDate)
    {
        if (validDateTime($expirationDate)) {
            $this->expirationDate = $expirationDate;
        } else {
            $this->erreurs['expirationDate'] = self::EXPIRATIONDATE_INVALIDE;
        }
    }


    //getters
    public function id()
    {
        return $this->id;
    }

    public function newRT()
    {
        return $this->newRT;
    }

    public function oldRT()
    {
        return $this->oldRT;
    }

    public function expirationDate()
    {
        return $this->expirationDate;
    }
}