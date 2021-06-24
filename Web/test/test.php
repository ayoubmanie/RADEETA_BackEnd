<?php
class Agent
{
    public $id;
    public $nom;
    public $prenom;
    public $dateNaissance;
}
class test
{
    public $db;
    // public $attr1;
    // public $attr2;


    public function __construct()
    {
        $this->db = new \PDO('mysql:host=localhost;dbname=radeeta', 'root', '');
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // $this->attr1 = 'ok 1';
        // $this->attr2 = 'ok 2';
    }

    public function get($id)
    {
        $requete = $this->db->prepare('SELECT * FROM agent WHERE id = :id');
        $requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Agent');

        if ($agent = $requete->fetch()) {
            return $agent;
        }

        return null;
    }
    public function getlist()
    {
        $requete = $this->db->prepare('SELECT * FROM agent');
        $requete->execute();

        $requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Agent');
        $listeAgent = $requete->fetchAll();

        $requete->closeCursor();

        return $listeAgent;
    }
}

$o = new test();

print_r($o->get(3));

// var_dump($o);