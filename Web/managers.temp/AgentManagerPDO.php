<?php

class AgentManagerPDO
{
    protected function add(Agent $agent)
    {
        $requete = $this->dao->prepare("INSERT INTO agent SET matricule = :matricule, cin = :cin, nom = :nom, prenom = :prenom, genre = :genre, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance, dateRecrute = :dateRecrute, dateTitulation = :dateTitulation, situationFamiliale = :situationFamiliale, nomEpoux = :nomEpoux, fonctionEpoux = :fonctionEpoux, adresse = :adresse, telPersonnel = :telPersonnel, telProfessionnel = :telProfessionnel, presence = :presence, suprrimee = :suprrimee");

        $requete->bindValue(":matricule", $agent->matricule());
        $requete->bindValue(":cin", $agent->cin());
        $requete->bindValue(":nom", $agent->nom());
        $requete->bindValue(":prenom", $agent->prenom());
        $requete->bindValue(":genre", $agent->genre());
        $requete->bindValue(":dateNaissance", $agent->dateNaissance());
        $requete->bindValue(":lieuNaissance", $agent->lieuNaissance());
        $requete->bindValue(":dateRecrute", $agent->dateRecrute());
        $requete->bindValue(":dateTitulation", $agent->dateTitulation());
        $requete->bindValue(":situationFamiliale", $agent->situationFamiliale());
        $requete->bindValue(":nomEpoux", $agent->nomEpoux());
        $requete->bindValue(":fonctionEpoux", $agent->fonctionEpoux());
        $requete->bindValue(":adresse", $agent->adresse());
        $requete->bindValue(":telPersonnel", $agent->telPersonnel());
        $requete->bindValue(":telProfessionnel", $agent->telProfessionnel());
        $requete->bindValue(":presence", $agent->presence());
        $requete->bindValue(":suprrimee", $agent->suprrimee());

        $requete->execute();
    }
}