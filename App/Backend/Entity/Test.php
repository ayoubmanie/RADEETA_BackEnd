<?php

namespace Entity;

use \Lib\Entity;
use Throwable;

class Test extends Entity
{
    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected string $dateNaissance;
    protected string $dateModif; //datemodif is only mentionned here to let the dev know that this this table has this column

    const ID_INVALIDE = 1;
    const NOM_INVALIDE = 2;
    const PRENOM_INVALIDE = 3;
    const DATENAISSANCE_INVALIDE = 4;


    //manager needs

    //attributes for the add method in the exempleManager
    public function  addAttrs(): array
    {
        return  [
            'nom',
            'prenom',
            'dateNaissance',
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
            $this->erreurs['id'] = self::ID_INVALIDE;
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom) && !empty($nom)) {
            $this->nom = $nom;
        } else {
            $this->erreurs['nom'] = self::NOM_INVALIDE;
        }
    }

    public function setPrenom($prenom)
    {
        if (is_string($prenom) && !empty($prenom)) {
            $this->prenom = $prenom;
        } else {
            $this->erreurs['prenom'] = self::PRENOM_INVALIDE;
        }
    }

    public function setDateNaissance($dateNaissance)
    {
        if (validDate($dateNaissance)) {
            $this->dateNaissance = $dateNaissance;
        } else {
            $this->erreurs['dateNaissance'] = self::DATENAISSANCE_INVALIDE;
        }
    }

    public function setDateModif($dateModif)
    {
        $this->dateModif = date("Y-m-d h:i:s");
    }


    //getters
    public function id()
    {
        return $this->id;
    }

    public function nom()
    {
        return $this->nom;
    }

    public function prenom()
    {
        return $this->prenom;
    }

    public function dateNaissance()
    {
        return $this->dateNaissance;
    }

    public function dateModif()
    {
        return $this->dateModif;
    }
}