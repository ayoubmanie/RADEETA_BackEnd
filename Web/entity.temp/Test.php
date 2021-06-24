<?php

namespace Entity;

use \Lib\Entity;

class Test extends Entity
{
    protected
        $id,
        $nom,
        $prenom,
        $dateNaissance;

    const ID_INVALIDE = 1;
    const NOM_INVALIDE = 2;
    const PRENOM_INVALIDE = 3;
    const DATENAISSANCE_INVALIDE = 4;

    public function isNew()
    {
        return !empty($this->id);
    }

    public function setId($id)
    {
        if (is_string($id) && !empty($id)) {
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

    public function setPrenom($prenom)
    {
        if (is_string($prenom) && !empty($prenom)) {
            $this->prenom = $prenom;
        } else {
            $this->erreurs[] = self::PRENOM_INVALIDE;
        }
    }

    public function setDateNaissance($dateNaissance)
    {
        if (!$this->validDate($dateNaissance)) {
            $this->dateNaissance = $dateNaissance;
        } else {
            $this->erreurs[] = self::DATENAISSANCE_INVALIDE;
        }
    }

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
}