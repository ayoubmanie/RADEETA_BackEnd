<?php

namespace Lib;

abstract class Entity
{
    protected $invalidEntity;
    protected $rawPostData;
    protected $updateAttrs = [];
    protected $getAttrs = [];
    protected $erreurs = [];
    protected $config;
    protected $searchType;

    protected int $LIMIT = -1;
    protected int $OFFSET = -1;

    use Hydrator;

    abstract public function addAttrs(): array;
    abstract public function searchKeys(): array;
    abstract public function classId();


    //attributes for the updated, they are not seted by the user
    //this method is called by the entity constructor
    abstract public function autoUpdateAttrs(): array;

    public function __construct($action, array $donnees = [], $config, $searchType = '')
    {
        $this->config = $config;


        if (!empty($donnees)) {

            $this->searchType = $searchType;

            $this->rawPostData = $donnees;

            if ($action == 'add') {
                $this->hydrate(array_flip($this->addAttrs()), $donnees, $action);
            } elseif ($action == 'update') {


                $id  = $this->classId();

                //check if there is an empty id 
                $value = trim($id);
                if (empty($value)) throw new \InvalidArgumentException("unknown class id");

                //check if there more post data than post id
                // if (empty(array_diff(array_flip($donnees), [$id]))) {
                //     // throw new \Exception("no post data found to update", 1);
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

                // if (array_key_exists($id, $donnees)) {



                $tempUpdateAttrs = $donnees;
                $tempUpdateAttrs[$id] = null;

                $this->hydrate($tempUpdateAttrs, $donnees, $action);

                // } else {

                //     throw new \InvalidArgumentException("post data '" . $id . "' is missing,");
                // }
            } elseif ($action == 'get') {


                $attrsUsedForSearch = array_merge(["LIMIT", "OFFSET"], $this->searchKeys());

                // print_r(array_keys($donnees));
                // exit;



                if (!empty(array_diff(array_keys($donnees), $attrsUsedForSearch))) {
                    $this->erreurs["invalidSearchData"] = "attributes not existing";
                } else {
                    $tempGetAttrs = array_intersect($this->searchKeys(), array_keys($donnees));

                    unset($tempGetAttrs['LIMIT']);
                    unset($tempGetAttrs['OFFSET']);

                    if (isset($donnees["LIMIT"]))  $this->setLIMIT($donnees["LIMIT"]);
                    if (isset($donnees["OFFSET"])) $this->setOFFSET($donnees["OFFSET"]);


                    foreach ($tempGetAttrs as $attr) {

                        //default search , no operator and one value
                        if (!is_array($donnees[$attr])) {
                            $config = $this->config->get();
                            $default =  $config->Backend->ManagerPDO->search->defaultOperator;

                            $newDonnees["operator"] = $default;
                            $newDonnees["values"] = $donnees[$attr];
                            $donnees[$attr] = [$newDonnees];
                        }

                        $donnees[$attr] = formatJsonToArray($donnees[$attr]);

                        foreach ($donnees[$attr] as $k => $element) {


                            if (!isset($element['values'])) {

                                $this->erreurs[$attr][$k]['values'] = "empty";
                            } else {

                                if (!is_array($element['values'])) $element['values'] = [$element['values']];

                                foreach ($element['values']  as $ke => $v) {

                                    if (gettype($v) == gettype($this->$attr)) {
                                        $this->getAttrs[$attr][$k]['values'][] = $v;
                                    } else {
                                        $this->erreurs[$attr][$k]['values'] = "invalid";
                                        break;
                                    }
                                }
                            }

                            if (!isset($element['operator'])) {

                                $this->erreurs[$attr][$k]['operator'] = "empty";
                            } else {

                                $config = $this->config->get();
                                $operators =  $config->Backend->ManagerPDO->search->operators;

                                if (!in_array($element['operator'], $operators)) {
                                    $this->erreurs[$attr][$k]['operator'] = "invalid";
                                } else {
                                    $this->getAttrs[$attr][$k]['operator'] = $element['operator'];
                                }
                            }
                        }
                    }
                }
            } elseif ($action == "search") {

                if ($this->searchType == 'where') {

                    $attrsUsedForSearch = $this->searchKeys();

                    // print_r(array_keys($donnees));
                    // exit;



                    if (!empty(array_diff(array_keys($donnees), $attrsUsedForSearch))) {

                        $this->erreurs["invalidSearchData"] = "attributes not existing";
                    } else {

                        $tempGetAttrs = array_intersect($this->searchKeys(), array_keys($donnees));

                        foreach ($tempGetAttrs as $attr) {

                            //default search , no operator and one value
                            if (!is_array($donnees[$attr])) {
                                $config = $this->config->get();
                                $default =  $config->Backend->ManagerPDO->search->defaultOperator;

                                $newDonnees["operator"] = $default;
                                $newDonnees["values"] = $donnees[$attr];
                                $donnees[$attr] = [$newDonnees];
                            }

                            $donnees[$attr] = formatJsonToArray($donnees[$attr]);

                            foreach ($donnees[$attr] as $k => $element) {


                                if (!isset($element['values'])) {

                                    $this->erreurs[$attr][$k]['values'] = "empty";
                                } else {

                                    if (!is_array($element['values'])) $element['values'] = [$element['values']];

                                    foreach ($element['values']  as $ke => $v) {

                                        if (gettype($v) == gettype($this->$attr)) {
                                            $this->getAttrs[$attr][$k]['values'][] = $v;
                                        } else {
                                            $this->erreurs[$attr][$k]['values'] = "invalid";
                                            break;
                                        }
                                    }
                                }

                                if (!isset($element['operator'])) {

                                    $this->erreurs[$attr][$k]['operator'] = "empty";
                                } else {

                                    $config = $this->config->get();
                                    $operators =  $config->Backend->ManagerPDO->search->operators;

                                    if (!in_array($element['operator'], $operators)) {
                                        $this->erreurs[$attr][$k]['operator'] = "invalid";
                                    } else {
                                        $this->getAttrs[$attr][$k]['operator'] = $element['operator'];
                                    }
                                }
                            }
                        }
                    }
                } elseif ($this->searchType == 'select') {


                    if (isset($donnees["LIMIT"]))  $this->setLIMIT($donnees["LIMIT"]);
                    if (isset($donnees["OFFSET"])) $this->setOFFSET($donnees["OFFSET"]);


                    $attrsUsedForSearch = $this->searchKeys();


                    if (isset($donnees['columns'])) {
                        if (!empty(array_diff($donnees['columns'], $attrsUsedForSearch))) {
                            $this->erreurs["invalidSearchData"] = "attributes not existing";
                        } else {
                            $this->getAttrs['columns'] = $donnees['columns'];
                        }
                    }
                }
            }
        }
    }


    public function setLIMIT($LIMIT)
    {
        if (is_numeric($LIMIT) && $LIMIT > 0) {
            $this->LIMIT = $LIMIT;
        } else {
            $this->erreurs['LIMIT'] = "invalid";
        }
    }

    public function setOFFSET($OFFSET)
    {
        if (is_numeric($OFFSET) && $OFFSET >= 0) {
            $this->OFFSET = $OFFSET;
        } else {
            $this->erreurs['OFFSET'] = "invalid";
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

    public function getAttrs()
    {
        return $this->getAttrs;
    }

    public function rowCount()
    {
        return $this->rowCount;
    }

    public function offset()
    {
        return $this->offset;
    }
}