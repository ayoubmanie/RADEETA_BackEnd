<?php

namespace Entity;

use \Lib\Entity;

class Agent extends Entity
{
    protected
        $matricule,
        $cin,
        $nom,
        $prenom,
        $genre,
        $dateNaissance,
        $lieuNaissance,
        $dateRecrute,
        $dateTitulation,
        $situationFamiliale,
        $nomEpoux,
        $fonctionEpoux,
        $adresse,
        $telPersonnel,
        $telProfessionnel,
        $presence,
        $suprrimee,
        $dateCreation,
        $creerPar,
        $dateModif,
        $modifierPar;

    const MATRICULE_INVALIDE = 1;
    const CIN_INVALIDE = 2;
    const NOM_INVALIDE = 3;
    const PRENOM_INVALIDE = 4;
    const GENRE_INVALIDE = 5;
    const DATENAISSANCE_INVALIDE = 6;
    const LIEUNAISSANCE_INVALIDE = 7;
    const DATERECRUTE_INVALIDE = 8;
    const DATETITULATION_INVALIDE = 9;
    const SITUATIONFAMILIALE_INVALIDE = 10;
    const NOMEPOUX_INVALIDE = 11;
    const FONCTIONEPOUX_INVALIDE = 12;
    const ADRESSE_INVALIDE = 13;
    const TELPERSONNEL_INVALIDE = 14;
    const TELPROFESSIONNEL_INVALIDE = 15;
    const PRESENCE_INVALIDE = 16;
    const SUPRRIMEE_INVALIDE = 17;
    const DATECREATION_INVALIDE = 18;
    const CREERPAR_INVALIDE = 19;
    const DATEMODIF_INVALIDE = 20;
    const MODIFIERPAR_INVALIDE = 21;

    public function setMatricule($matricule)
    {
        if (is_string($matricule) && empty($matricule)) {
            $this->matricule = $matricule;
        } else {
            $this->erreurs[] = self::MATRICULE_INVALIDE;
        }
    }

    public function setCin($cin)
    {
        if (is_string($cin) && empty($cin)) {
            $this->cin = $cin;
        } else {
            $this->erreurs[] = self::CIN_INVALIDE;
        }
    }

    public function setNom($nom)
    {
        if (is_string($nom) && empty($nom)) {
            $this->nom = $nom;
        } else {
            $this->erreurs[] = self::NOM_INVALIDE;
        }
    }

    public function setPrenom($prenom)
    {
        if (is_string($prenom) && empty($prenom)) {
            $this->prenom = $prenom;
        } else {
            $this->erreurs[] = self::PRENOM_INVALIDE;
        }
    }

    public function setGenre($genre)
    {
        if (is_string($genre) && empty($genre)) {
            $this->genre = $genre;
        } else {
            $this->erreurs[] = self::GENRE_INVALIDE;
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

    public function setLieuNaissance($lieuNaissance)
    {
        if (is_string($lieuNaissance) && empty($lieuNaissance)) {
            $this->lieuNaissance = $lieuNaissance;
        } else {
            $this->erreurs[] = self::LIEUNAISSANCE_INVALIDE;
        }
    }

    public function setDateRecrute($dateRecrute)
    {
        if (!$this->validDate($dateRecrute)) {
            $this->dateRecrute = $dateRecrute;
        } else {
            $this->erreurs[] = self::DATERECRUTE_INVALIDE;
        }
    }

    public function setDateTitulation($dateTitulation)
    {
        if (!$this->validDate($dateTitulation)) {
            $this->dateTitulation = $dateTitulation;
        } else {
            $this->erreurs[] = self::DATETITULATION_INVALIDE;
        }
    }

    public function setSituationFamiliale($situationFamiliale)
    {
        if (is_string($situationFamiliale) && empty($situationFamiliale)) {
            $this->situationFamiliale = $situationFamiliale;
        } else {
            $this->erreurs[] = self::SITUATIONFAMILIALE_INVALIDE;
        }
    }

    public function setNomEpoux($nomEpoux)
    {
        if (is_string($nomEpoux) && empty($nomEpoux)) {
            $this->nomEpoux = $nomEpoux;
        } else {
            $this->erreurs[] = self::NOMEPOUX_INVALIDE;
        }
    }

    public function setFonctionEpoux($fonctionEpoux)
    {
        if (is_string($fonctionEpoux) && empty($fonctionEpoux)) {
            $this->fonctionEpoux = $fonctionEpoux;
        } else {
            $this->erreurs[] = self::FONCTIONEPOUX_INVALIDE;
        }
    }

    public function setAdresse($adresse)
    {
        if (is_string($adresse) && empty($adresse)) {
            $this->adresse = $adresse;
        } else {
            $this->erreurs[] = self::ADRESSE_INVALIDE;
        }
    }

    public function setTelPersonnel($telPersonnel)
    {
        if (is_string($telPersonnel) && empty($telPersonnel)) {
            $this->telPersonnel = $telPersonnel;
        } else {
            $this->erreurs[] = self::TELPERSONNEL_INVALIDE;
        }
    }

    public function setTelProfessionnel($telProfessionnel)
    {
        if (is_string($telProfessionnel) && empty($telProfessionnel)) {
            $this->telProfessionnel = $telProfessionnel;
        } else {
            $this->erreurs[] = self::TELPROFESSIONNEL_INVALIDE;
        }
    }

    public function setPresence($presence)
    {
        if (is_string($presence) && empty($presence)) {
            $this->presence = $presence;
        } else {
            $this->erreurs[] = self::PRESENCE_INVALIDE;
        }
    }

    public function setSuprrimee($suprrimee)
    {
        if (is_string($suprrimee) && empty($suprrimee)) {
            $this->suprrimee = $suprrimee;
        } else {
            $this->erreurs[] = self::SUPRRIMEE_INVALIDE;
        }
    }

    public function setDateCreation($dateCreation)
    {
        if (!$this->validDate($dateCreation)) {
            $this->dateCreation = $dateCreation;
        } else {
            $this->erreurs[] = self::DATECREATION_INVALIDE;
        }
    }

    public function setCreerPar($creerPar)
    {
        if (is_string($creerPar) && empty($creerPar)) {
            $this->creerPar = $creerPar;
        } else {
            $this->erreurs[] = self::CREERPAR_INVALIDE;
        }
    }

    public function setDateModif($dateModif)
    {
        if (!$this->validDate($dateModif)) {
            $this->dateModif = $dateModif;
        } else {
            $this->erreurs[] = self::DATEMODIF_INVALIDE;
        }
    }

    public function setModifierPar($modifierPar)
    {
        if (is_string($modifierPar) && empty($modifierPar)) {
            $this->modifierPar = $modifierPar;
        } else {
            $this->erreurs[] = self::MODIFIERPAR_INVALIDE;
        }
    }

    public function matricule()
    {
        return $this->matricule;
    }

    public function cin()
    {
        return $this->cin;
    }

    public function nom()
    {
        return $this->nom;
    }

    public function prenom()
    {
        return $this->prenom;
    }

    public function genre()
    {
        return $this->genre;
    }

    public function dateNaissance()
    {
        return $this->dateNaissance;
    }

    public function lieuNaissance()
    {
        return $this->lieuNaissance;
    }

    public function dateRecrute()
    {
        return $this->dateRecrute;
    }

    public function dateTitulation()
    {
        return $this->dateTitulation;
    }

    public function situationFamiliale()
    {
        return $this->situationFamiliale;
    }

    public function nomEpoux()
    {
        return $this->nomEpoux;
    }

    public function fonctionEpoux()
    {
        return $this->fonctionEpoux;
    }

    public function adresse()
    {
        return $this->adresse;
    }

    public function telPersonnel()
    {
        return $this->telPersonnel;
    }

    public function telProfessionnel()
    {
        return $this->telProfessionnel;
    }

    public function presence()
    {
        return $this->presence;
    }

    public function suprrimee()
    {
        return $this->suprrimee;
    }

    public function dateCreation()
    {
        return $this->dateCreation;
    }

    public function creerPar()
    {
        return $this->creerPar;
    }

    public function dateModif()
    {
        return $this->dateModif;
    }

    public function modifierPar()
    {
        return $this->modifierPar;
    }
}