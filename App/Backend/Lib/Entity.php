<?php

namespace Lib;

abstract class Entity
{
    protected $updateAttrs = [];
    protected $erreurs = [];
    protected $config;

    use Hydrator;

    abstract public function addAttrs(): array;
    abstract public function classId();
    abstract public function autoUpdateAttrs(): array;

    public function __construct($action, array $donnees = [], $config)
    {
        $this->config = $config;

        if (!empty($donnees)) {
            if ($action == 'add') {
                $this->hydrate(array_flip($this->addAttrs()), $donnees, $action);
            } elseif ($action == 'update') {


                $id  = $this->classId();
                // if (!is_array($ids)) {
                //     throw new \Exception("class id/ids must be an array");
                // }

                //check if there is an empty id 
                $value = trim($id);
                if (empty($value)) throw new \InvalidArgumentException("unknown class id");
                // foreach ($ids as $key => $value) {
                //     $value = trim($value);
                //     if (empty($value))
                //         throw new \InvalidArgumentException("the id is missing");
                // }


                //check if there is an attribute that is not updated from the user, like the dateModif ...
                foreach ($this->autoUpdateAttrs() as $attr) {
                    $attr = trim($attr);
                    if (empty($attr))
                        throw new \InvalidArgumentException("an empty autoUpdateAttrs is declared in class : " . get_class($this) . ",");
                    else {
                        $donnees[$attr] = '';
                    }
                }


                // if (!empty($ids) && array_keys_exists($ids, $donnees)) {
                if (array_key_exists($id, $donnees)) {

                    $this->hydrate($donnees, $donnees, $action);
                } else {
                    // $idsString = "";
                    // foreach ($ids as $id) {
                    //     $idsString .= $id . ', ';
                    // }

                    throw new \InvalidArgumentException("post data '" . $id . "' is missing,");
                }
            }
        }
    }




    public function isValid()
    {

        if (empty($this->erreurs)) {
            return true;
        } else {
            throw new \RuntimeException(formErrorMsg($this));
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
}