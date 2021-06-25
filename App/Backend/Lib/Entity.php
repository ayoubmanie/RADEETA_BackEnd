<?php

namespace Lib;

abstract class Entity
{
    protected $invalidEntity;
    protected $rawPostData;
    protected $updateAttrs = [];
    protected $erreurs = [];
    protected $config;

    use Hydrator;

    abstract public function addAttrs(): array;
    abstract public function classId();

    //attributes for the updated, they are not seted by the user
    //this method is called by the entity constructor
    abstract public function autoUpdateAttrs(): array;

    public function __construct($action, array $donnees = [], $config)
    {
        $this->config = $config;

        if (!empty($donnees)) {
            $this->rawPostData = $donnees;
            if ($action == 'add') {
                $this->hydrate(array_flip($this->addAttrs()), $donnees, $action);
            } elseif ($action == 'update') {


                $id  = $this->classId();

                //check if there is an empty id 
                $value = trim($id);
                if (empty($value)) throw new \InvalidArgumentException("unknown class id");

                //check if there more post data than post id
                if (empty(array_diff(array_flip($donnees), [$id]))) {
                    // throw new \Exception("no post data found to update", 1);
                }


                //check if there is an attribute that is not updated from the user, like the dateModif ...
                foreach ($this->autoUpdateAttrs() as $attr) {
                    $attr = trim($attr);
                    if (empty($attr))
                        throw new \InvalidArgumentException("an empty autoUpdateAttrs is declared in class : " . get_class($this) . ",");
                    else {
                        $donnees[$attr] = '';
                    }
                }

                // if (array_key_exists($id, $donnees)) {



                $tempUpdateAttrs = $donnees;
                $tempUpdateAttrs[$id] = null;

                $this->hydrate($tempUpdateAttrs, $donnees, $action);

                // } else {

                //     throw new \InvalidArgumentException("post data '" . $id . "' is missing,");
                // }
            }
        }
    }




    public function isValid()
    {

        if (empty($this->erreurs)) {
            return true;
        } else {
            $this->invalidEntity =  [
                'postData' => $this->rawPostData,
                'errorMessage' => $this->erreurs
            ];
            return false;
        }
    }




    public function erreurs()
    {
        return $this->erreurs;
    }

    public function updateAttrs()
    {
        return $this->updateAttrs;
    }

    public function invalidEntity()
    {
        return $this->invalidEntity;
    }
}