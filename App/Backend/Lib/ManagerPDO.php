<?php

namespace Lib;

// use InvalidArgumentException;

trait ManagerPDO
{

    protected $invalidEntities = [];
    protected $validEntitiesResult = [];
    // public function update(Entity $entity)
    // {

    //     if ($entity->isValid()) {
    //         $tableName = $this->tableName($entity);

    //         $ids  = $entity->classIds();

    //         $attrs = array_diff($entity->updateAttrs(), $ids);
    //         $queryUpdateAttrs = $this->querryAttrs($attrs);

    //         $queryIds = "";
    //         foreach ($ids as $key => $value) {
    //             $queryIds .= " $value = :$value";
    //             if (array_key_last($ids) != $key)  $queryIds .= ' AND ';
    //         }

    //         $requete = "UPDATE $tableName SET $queryUpdateAttrs WHERE $queryIds";

    //         $requete = $this->dao->prepare($requete);
    //         $requete = $this->bindAllAttrs($requete, $entity, $entity->updateAttrs());
    //         $requete->execute();
    //     }
    // }

    public function update($entities)
    {
        if (!is_array($entities)) $entities = [$entities];

        $refrenceEntity = $entities[0];
        $tableName = $this->tableName($refrenceEntity);
        $queryColumns = '';

        $id  = $refrenceEntity->classId();



        $allUpdateAttrs = [];
        $ids = '';
        $finalEntities = [];
        foreach ($entities as $key => $entity) {

            if ($entity->isValid()) {
                //remove the id
                $attrs = array_diff($entity->updateAttrs(), [$id]);


                foreach ($attrs as $attr) {
                    $allUpdateAttrs[$attr][] = "WHEN id = :$id$key THEN :$attr$key";
                }

                $ids .= ":$id$key";

                if (array_key_last($entities) != $key)  $ids .= ' , ';
                $finalEntities[$key] = $entity;
            } else {
                $this->invalidEntities[$key] = $entity->invalidEntity();
            }
        }


        if (!empty($allUpdateAttrs)) {

            $queryUpdateAttrs = '';

            foreach ($allUpdateAttrs as $attr => $values) {

                $queryUpdateAttrs .= "$attr = ( CASE ";

                foreach ($values as $value) {
                    $queryUpdateAttrs .= " $value ";
                }

                $queryUpdateAttrs .= "ELSE $attr END)";
                if (array_key_last($allUpdateAttrs) != $attr)  $queryUpdateAttrs .= ' , ';
            }



            $requete = "UPDATE $tableName SET $queryUpdateAttrs WHERE $id in ($ids)";

            //in order to bind a parametter (ex :id) multiole times 
            $this->dao->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $requete = $this->dao->prepare($requete);

            // bind 
            foreach ($finalEntities as $key => $entity) {
                $requete = $this->bindAllAttrs($requete, $entity, $entity->updateAttrs(), $key);
            }

            // execute;
            $requete->execute();
        }
    }


    public function add($entities)
    {
        if (!is_array($entities)) $entities = [$entities];

        $refrenceEntity = $entities[0];
        $tableName = $this->tableName($refrenceEntity);
        $queryColumns = '';

        // $queryColumns
        foreach ($refrenceEntity->addAttrs() as $attr => $value) {
            $queryColumns .= $value;
            if (array_key_last($refrenceEntity->addAttrs()) != $attr)  $queryColumns .= ', ';
        }

        // $queryAddAttrs
        $queryAddAttrs = '';
        $finalEntities = [];
        foreach ($entities as $key => $entity) {

            // print_r($entity);
            // continue;
            if ($entity->isValid()) {
                $queryAddAttrs .= $this->querryAttrsAdd($refrenceEntity->addAttrs(), $key);
                if (array_key_last($entities) != $key)  $queryAddAttrs .= ', ';
                $finalEntities[$key] = $entity;
            } else {
                $this->invalidEntities[$key] = $entity->invalidEntity();
            }
        }

        // print_r($this->invalidEntities);
        // exit;
        if ($queryAddAttrs != '') {
            // query
            $requete = "INSERT INTO $tableName ($queryColumns) VALUES $queryAddAttrs";


            // prepare
            $requete = $this->dao->prepare($requete);

            // bind 
            foreach ($finalEntities as $key => $entity) {
                $requete = $this->bindAllAttrs($requete, $entity, $refrenceEntity->addAttrs(), $key);
            }
            // execute
            $requete->execute();
        }
    }


    protected function tableName(Entity $entity)
    {
        // DO NOT REMOVE!!! (to avoid a warning)
        $className = array_values(explode("\\", get_class($entity)));
        return  end($className);
    }

    protected function querryAttrsAdd($attrs, $neededkey)
    {
        $queryAttrs = '';
        foreach ($attrs as $key => $attr) {
            $queryAttrs .= ' :' . $attr . $neededkey . ' ';
            if (array_key_last($attrs) != $key)  $queryAttrs .= ', ';
        }
        return "($queryAttrs)";
    }


    protected function querryAttrsGet($attrs, $neededkey)
    {
        $queryAttrs = '';
        // print_r($attrs);
        // exit;
        foreach ($attrs as $key => $attr) {

            foreach ($attr as $keyy => $v) {
                $operator = $v["operator"];

                foreach ($v["values"] as $k => $value) {
                    $queryAttrs .= " $key $operator :$key$neededkey$keyy$k ";
                    if (array_key_last($v["values"]) != $k)  $queryAttrs .= ' AND ';
                }
                if (array_key_last($attr) != $keyy)  $queryAttrs .= ' AND ';
            }

            if (array_key_last($attrs) != $key)  $queryAttrs .= ' AND ';
        }


        return "($queryAttrs)";
    }

    // protected function querryAttrsGet($attrs, $neededkey)
    // {
    //     $queryAttrs = '';
    //     $queryEntityKey = '';

    //     foreach ($attrs as $key => $attr) {
    //         $queryAttrs .= "$attr = :$attr$neededkey";
    //         if (array_key_last($attrs) != $key)  $queryAttrs .= ' AND ';
    //     }
    //     return [
    //         "queryWHERE" => "($queryAttrs)",
    //         "queryCASE" => " WHEN ($queryAttrs) THEN $neededkey "
    //     ];
    // }


    protected function bindAllAttrs($requete, $entity, $attrs, $key = '')
    {

        foreach ($attrs as $attr) {
            // exit;
            $type = gettype($entity->$attr());

            if ($type == 'integer') {
                $requete->bindValue(":" . $attr . $key, $entity->$attr(), \PDO::PARAM_INT);
            } elseif ($type == 'string') {
                $requete->bindValue(":" . $attr . $key, $entity->$attr(), \PDO::PARAM_STR);
            } else {
                throw new \InvalidArgumentException("attribute type not specified in the class : '" . get_class($this->entity)) . "'";
            }
            // echo $attr . $key . ' : ' . $entity->$attr() . '__';
        }
        return $requete;
    }

    protected function bindAllGetAttrs($requete, $entity, $attrs, $key = '')
    {

        foreach ($attrs as $attr => $elements) {
            // exit;
            $type = gettype($entity->$attr());

            foreach ($elements as $k => $e) {

                foreach ($e["values"] as $keyy => $value) {

                    if ($type == 'integer') {
                        $requete->bindValue(":$attr$key$k$keyy", $value, \PDO::PARAM_INT);
                    } elseif ($type == 'string') {
                        $requete->bindValue(":$attr$key$k$keyy", $value, \PDO::PARAM_STR);
                    } else {
                        throw new \InvalidArgumentException("attribute type not specified in the class : '" . get_class($this->entity)) . "'";
                    }
                }
            }
        }

        return $requete;
    }

    public function get($entities)
    {


        if (!is_array($entities)) $entities = [$entities];

        foreach ($entities as $key => $entity) {
            $refrenceEntity = $entities[$key];
        }
        $tableName = $this->tableName($refrenceEntity);


        // $queryAddAttrs
        $queryConditions = '';
        $queryEntityKey = '';
        $finalEntities = [];
        $requete = [];


        // $allGetattrs = [];


        foreach ($entities as $key => $entity) {

            // print_r($entity);
            // continue;
            if ($entity->isValid()) {

                $queryConditions =  $this->querryAttrsGet($entity->getAttrs(), $key);

                $requete[] = "(SELECT *, '$key' as entity  FROM $tableName WHERE $queryConditions)";
                // foreach ($entity->getAttrs() as $attr) {
                //     $allGetattrs[$attr] = null;
                // }
                // if (array_key_last($entities) != $key)  $requete .= ' UNION ';
                $finalEntities[$key] = $entity;
            } else {
                $this->invalidEntities[$key] = $entity->invalidEntity();
            }
        }

        // print_r($this->invalidEntities);
        // exit;
        $requete = implode(" UNION ", $requete);

        if ($requete != '') {
            // query
            // $requete = "SELECT *, CASE $queryEntityKey END AS entity FROM $tableName WHERE $queryConditions";

            // exit;
            // echo $requete;
            // exit;

            $this->dao->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            // prepare
            $requete = $this->dao->prepare($requete);

            // bind 

            foreach ($finalEntities as $key => $entity) {

                $requete = $this->bindAllGetAttrs($requete, $entity, $entity->getAttrs(), $key);
            }

            // execute
            $requete->execute();
            // var_dump($result = $requete->fetchAll());
            // print_r($result);
            // exit;
            // $this->validEntitiesResult[$column][] = null;
            if ($result = $requete->fetchAll()) {
                // print_r($result);
                // exit;
                //                 // echo "$key - ";
                foreach ($result as $row) {
                    $column = $row["entity"];
                    // echo "$column - ";
                    unset($row["entity"]);
                    $this->validEntitiesResult[$column][] = $row;
                }
            }
        }
    }
    // public function get($entities)
    // {

    //     if (!is_array($entities)) $entities = [$entities];

    //     $refrenceEntity = $entities[0];
    //     $tableName = $this->tableName($refrenceEntity);
    //     $queryConsitions = '';


    //     // $queryAddAttrs
    //     $queryConditions = '';
    //     $queryEntityKey = '';
    //     $finalEntities = [];

    //     // $allGetattrs = [];
    //     foreach ($entities as $key => $entity) {

    //         // print_r($entity);
    //         // continue;
    //         if ($entity->isValid()) {
    //             $temp = $this->querryAttrsGet($entity->getAttrs(), $key);
    //             $queryConditions .= $temp["queryWHERE"];
    //             $queryEntityKey .=  $temp["queryCASE"];

    //             // foreach ($entity->getAttrs() as $attr) {
    //             //     $allGetattrs[$attr] = null;
    //             // }
    //             if (array_key_last($entities) != $key)  $queryConditions .= ' OR ';
    //             $finalEntities[$key] = $entity;
    //         } else {
    //             $this->invalidEntities[$key] = $entity->invalidEntity();
    //         }
    //     }

    //     // print_r($this->invalidEntities);
    //     // exit;
    //     if ($queryConditions != '') {
    //         // query
    //         $requete = "SELECT *, CASE $queryEntityKey END AS entity FROM $tableName WHERE $queryConditions";

    //         // exit;
    //         // echo $requete;
    //         // exit;
    //         $this->dao->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

    //         // prepare
    //         $requete = $this->dao->prepare($requete);

    //         // bind 

    //         foreach ($finalEntities as $key => $entity) {
    //             $requete = $this->bindAllAttrs($requete, $entity, $entity->getAttrs(), $key);
    //         }

    //         // execute
    //         $requete->execute();

    //         if ($result = $requete->fetchAll()) {
    //             //                 // echo "$key - ";
    //             foreach ($result as $row) {
    //                 $column = $row["entity"];
    //                 unset($row["entity"]);
    //                 $this->validEntitiesResult[$column][] = $row;
    //             }
    //         }
    //     }
    // }

    // public function get($entities)
    // {

    //     if (!is_array($entities)) $entities = [$entities];

    //     $refrenceEntity = $entities[0];
    //     $tableName = $this->tableName($refrenceEntity);
    //     $queryColumns = '';

    //     foreach ($entities as $key => $entity) {
    //         if ($entity->isValid()) {


    //             $whereAttrs = '';


    //             foreach ($entity->getAttrs() as $keyy => $attr) {

    //                 $whereAttrs .= "$attr = :$attr$key";
    //                 if (array_key_last($entity->getAttrs()) != $keyy)  $whereAttrs .= ' AND ';
    //             }

    //             // $rowCount = $entity->rowCount();
    //             // $offset = $entity->offset();


    //             // $requete  = "SELECT * FROM $tableName WHERE $whereAttrs LIMIT $rowCount OFFSET $offset";
    //             $requete  = "SELECT * FROM $tableName WHERE $whereAttrs ";

    //             $requete = $this->dao->prepare($requete);

    //             // the $key here could be set to null
    //             $requete = $this->bindAllAttrs($requete, $entity, $entity->getAttrs());

    //             $requete->execute();

    //             if ($result = $requete->fetchAll()) {
    //                 // echo "$key - ";

    //                 $this->validEntitiesResult[$key] = $result;
    //             }
    //         } else {
    //             $this->invalidEntities[$key] = $entity->invalidEntity();
    //         }
    //     }
    // }


    public function response()
    {
        //add the http status if there is an invalidEntities!!!

        //don't use array_merge , it causes reindexing
        $array = $this->invalidEntities + $this->validEntitiesResult;
        return (object)$array;
    }
}