<?php

namespace Entity;

use \lib\Entity;

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
        $moifierPar;

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


    public function isValid()
    {
        return !(empty($this->matricule) || empty($this->cin) || empty($this->nom) || empty($this->prenom) || empty($this->genre) || empty($this->dateNaissance) || empty($this->lieuNaissance) || empty($this->dateRecrute) || empty($this->dateTitulation) || empty($this->situationFamiliale) || empty($this->nomEpoux) || empty($this->fonctionEpoux) || empty($this->adresse) || empty($this->telPersonnel) || empty($this->telProfessionnel) || empty($this->presence) || empty($this->suprrimee));
    }

    public function setMatricule($matricule)
    {
        if (!is_string($matricule) || empty($matricule)) {
            $this->erreurs[] = self::MATRICULE_INVALIDE;
        }
        $this->matricule = $matricule;
    }

    public function setCin($cin)
    {
        if (!is_string($cin) || empty($cin)) {
            $this->erreurs[] = self::CIN_INVALIDE;
        }
        $this->cin = $cin;
    }


    public function setNom($nom)
    {
        if (!is_string($nom) || empty($nom)) {
            $this->erreurs[] = self::NOM_INVALIDE;
        }
        $this->nom = $nom;
    }

    public function setPrenom($prenom)
    {
        if (!is_string($prenom) || empty($prenom)) {
            $this->erreurs[] = self::PRENOM_INVALIDE;
        }
        $this->prenom = $prenom;
    }

    public function setGenre($genre)
    {
        if (!is_string($genre) || empty($genre)) {
            $this->erreurs[] = self::GENRE_INVALIDE;
        }
        $this->genre = $genre;
    }

    public function setDateNaissance($dateNaissance)
    {
        if (!$this->validDate($dateNaissance)) {
            $this->erreurs[] = self::DATENAISSANCE_INVALIDE;
        }
        $this->dateNaissance = $dateNaissance;
    }

    public function setLieuNaissance($lieuNaissance)
    {
        if (!is_string($lieuNaissance) || empty($lieuNaissance)) {
            $this->erreurs[] = self::LIEUNAISSANCE_INVALIDE;
        }
        $this->lieuNaissance = $lieuNaissance;
    }

    public function setDateRecrute($dateRecrute)
    {
        if (!$this->validDate($dateRecrute)) {
            $this->erreurs[] = self::DATERECRUTE_INVALIDE;
        }
        $this->dateRecrute = $dateRecrute;
    }

    public function setDateTitulation($dateTitulation)
    {
        if (!$this->validDate($dateTitulation)) {
            $this->erreurs[] = self::DATETITULATION_INVALIDE;
        }
        $this->dateTitulation = $dateTitulation;
    }

    public function setSituationFamiliale($situationFamiliale)
    {
        if (!is_string($situationFamiliale) || empty($situationFamiliale)) {
            $this->erreurs[] = self::SITUATIONFAMILIALE_INVALIDE;
        }
        $this->situationFamiliale = $situationFamiliale;
    }

    public function setNomEpoux($nomEpoux)
    {
        if (!is_string($nomEpoux) || empty($nomEpoux)) {
            $this->erreurs[] = self::NOMEPOUX_INVALIDE;
        }
        $this->nomEpoux = $nomEpoux;
    }

    public function setFonctionEpoux($fonctionEpoux)
    {
        if (!is_string($fonctionEpoux) || empty($fonctionEpoux)) {
            $this->erreurs[] = self::FONCTIONEPOUX_INVALIDE;
        }
        $this->fonctionEpoux = $fonctionEpoux;
    }

    public function setAdresse($adresse)
    {
        if (!is_string($adresse) || empty($adresse)) {
            $this->erreurs[] = self::ADRESSE_INVALIDE;
        }
        $this->adresse = $adresse;
    }

    public function setTelPersonnel($telPersonnel)
    {
        if (!is_string($telPersonnel) || empty($telPersonnel)) {
            $this->erreurs[] = self::TELPERSONNEL_INVALIDE;
        }
        $this->telPersonnel = $telPersonnel;
    }

    public function setTelProfessionnel($telProfessionnel)
    {
        if (!is_string($telProfessionnel) || empty($telProfessionnel)) {
            $this->erreurs[] = self::TELPROFESSIONNEL_INVALIDE;
        }
        $this->telProfessionnel = $telProfessionnel;
    }

    public function setPresence($presence)
    {
        if (!is_string($presence) || empty($presence)) {
            $this->erreurs[] = self::PRESENCE_INVALIDE;
        }
        $this->presence = $presence;
    }

    public function setSuprrimee($suprrimee)
    {
        if (!is_string($suprrimee) || empty($suprrimee)) {
            $this->erreurs[] = self::SUPRRIMEE_INVALIDE;
        }
        $this->suprrimee = $suprrimee;
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


    public function isNew()
    {
        return empty($this->matricule);
    }
}