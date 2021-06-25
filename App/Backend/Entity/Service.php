<?php

namespace Entity;

use \Lib\Entity;

class Service extends Entity
{

    //attributes
    protected int $id;
    protected string $nom;

    const ID_INVALIDE = 1;
    const NOM_INVALIDE = 2;


    //manager needs

    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'nom'
        ];
    }

    //attribute for the update method in the exempleManager , WHERE id
    public function classId()
    {
        return  'id';
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
            $this->erreurs[] = self::ID_INVALIDE;
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom) && !empty($nom)) {
            $this->nom = $nom;
        } else {
            $this->erreurs[] = self::NOM_INVALIDE;
        }
    }

    // getters
    public function id()
    {
        return $this->id;
    }

    public function nom()
    {
        return $this->nom;
    }
}