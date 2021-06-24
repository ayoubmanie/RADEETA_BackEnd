<?php

namespace Entity;

use \Lib\Entity;

class Service extends Entity
{
    protected
        $id,
        $nom;

    const ID_INVALIDE = 1;
    const NOM_INVALIDE = 2;

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

    public function id()
    {
        return $this->id;
    }

    public function nom()
    {
        return $this->nom;
    }
}