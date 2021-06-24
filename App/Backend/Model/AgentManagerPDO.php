<?php

namespace Model;

use \Entity\Agent;

class AgentManagerPDO extends AgentManager
{
        protected function bindValues($requete, Agent $agent)
        {
                $requete->bindValue(":cin", $agent->cin(), \PDO::PARAM_STR);
                $requete->bindValue(":nom", $agent->nom(), \PDO::PARAM_STR);
                $requete->bindValue(":prenom", $agent->prenom(), \PDO::PARAM_STR);
                $requete->bindValue(":genre", $agent->genre(), \PDO::PARAM_INT);
                $requete->bindValue(":dateNaissance", $agent->dateNaissance(), \PDO::PARAM_STR);
                $requete->bindValue(":lieuNaissance", $agent->lieuNaissance(), \PDO::PARAM_STR);
                $requete->bindValue(":dateRecrute", $agent->dateRecrute(), \PDO::PARAM_STR);
                $requete->bindValue(":dateTitulation", $agent->dateTitulation(), \PDO::PARAM_STR);
                $requete->bindValue(":situationFamiliale", $agent->situationFamiliale(), \PDO::PARAM_STR);
                $requete->bindValue(":nomEpoux", $agent->nomEpoux(), \PDO::PARAM_STR);
                $requete->bindValue(":fonctionEpoux", $agent->fonctionEpoux(), \PDO::PARAM_STR);
                $requete->bindValue(":adresse", $agent->adresse(), \PDO::PARAM_STR);
                $requete->bindValue(":telPersonnel", $agent->telPersonnel(), \PDO::PARAM_STR);
                $requete->bindValue(":telProfessionnel", $agent->telProfessionnel(), \PDO::PARAM_STR);
                $requete->bindValue(":presence", $agent->presence());
                $requete->bindValue(":suprrimee", $agent->suprrimee());
                $requete->bindValue(":modifierPar", $agent->modifierPar());

                return $requete;
        }
        protected function add(Agent $agent)
        {
                $requete = $this->dao->prepare("INSERT INTO agent SET cin = :cin, nom = :nom, prenom = :prenom, genre = :genre, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance, dateRecrute = :dateRecrute, dateTitulation = :dateTitulation, situationFamiliale = :situationFamiliale, nomEpoux = :nomEpoux, fonctionEpoux = :fonctionEpoux, adresse = :adresse, telPersonnel = :telPersonnel, telProfessionnel = :telProfessionnel, presence = :presence, suprrimee = :suprrimee, dateCreation = NOW(), creerPar = :creerPar, dateModif = NOW(), modifierPar = :modifierPar");

                $requete->bindValue(":creerPar", $agent->creerPar());
                $requete = $this->bindValues($requete, $agent);

                $requete->execute();
        }

        protected function modify(Agent $agent)
        {
                $requete = $this->dao->prepare('UPDATE agent SET  cin = :cin, nom = :nom, prenom = :prenom, genre = :genre, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance, dateRecrute = :dateRecrute, dateTitulation = :dateTitulation, situationFamiliale = :situationFamiliale, nomEpoux = :nomEpoux, fonctionEpoux = :fonctionEpoux, adresse = :adresse, telPersonnel = :telPersonnel, telProfessionnel = :telProfessionnel, presence = :presence, suprrimee = :suprrimee, dateModif = NOW(), modifierPar = :modifierPar WHERE matricule = :matricule');

                $requete = $this->bindValues($requete, $agent);
                $requete->bindValue(":matricule", $agent->matricule(), \PDO::PARAM_INT);

                $requete->execute();
        }

        public function delete($matricule)
        {
                $this->dao->exec('DELETE FROM agent WHERE matricule = ' . (int) $matricule);
        }

        public function count()
        {
                return $this->dao->query('SELECT COUNT(*) FROM agent')->fetchColumn();
        }

        public function getList($debut = -1, $limite = -1)
        {
                $sql = 'SELECT * FROM agent ORDER BY matricule DESC';

                if ($debut != -1 || $limite != -1) {
                        $sql .= ' LIMIT ' . (int) $limite . ' OFFSET ' . (int) $debut;
                }

                $requete = $this->dao->query($sql);
                $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Agent');

                $listeAgent = $requete->fetchAll();

                $requete->closeCursor();

                return $listeAgent;
        }

        public function getUnique($matricule)
        {
                $requete = $this->dao->prepare('SELECT * FROM agent WHERE matricule = :matricule');
                $requete->bindValue(':matricule', (int) $matricule, \PDO::PARAM_INT);
                $requete->execute();

                $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Agent');

                if ($agent = $requete->fetch()) {
                        return  $agent;
                }

                return null;
        }
}