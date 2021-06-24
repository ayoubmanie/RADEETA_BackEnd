<?php

namespace Model;

use \Lib\Manager;
use \Entity\Agent;

abstract class AgentManager extends Manager
{

    /**
     * Méthode permettant d'ajouter un agent.
     * @param $agent agent L'agent à ajouter
     * @return void
     */
    abstract protected function add(Agent $agent);

    /**
     * Méthode permettant de modifier un agent.
     * @param $agent agent l'agent à modifier
     * @return void
     */
    abstract protected function modify(Agent $agent);

    /**
     * Méthode permettant de supprimer un agent.
     * @param $id int L'identifiant de l'agent à supprimer
     * @return void
     */
    abstract public function delete($matricule);

    /**
     * Méthode permettant d'enregistrer un agent.
     * @param $agent agent l'agent à enregistrer
     * @see self::add()
     * @see self::modify()
     * @return void
     */
    public function save(Agent $agent)
    {
        if ($agent->isValid()) {
            $agent->isNew() ? $this->add($agent) : $this->modify($agent);
        } else {
            throw new \RuntimeException('L\'agent doit être validée pour être enregistrée');
        }
    }

    /**
     * Méthode renvoyant le nombre d'gent total.
     * @return int
     */
    abstract public function count();

    /**
     * Méthode retournant une liste d'agent demandée.
     * @param $debut int La première agent à sélectionner
     * @param $limite int Le nombre d'agent à sélectionner
     * @return array La liste des agents. Chaque entrée est une instance d'aagent.
     */
    abstract public function getList($debut = -1, $limite = -1);

    /**
     * Méthode retournant un agent précis.
     * @param $id int L'identifiant de la agent à récupérer
     * @return agent L'agent demandée
     */
    abstract public function getUnique($id);
}