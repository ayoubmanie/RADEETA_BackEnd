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

        $refrenceEntity = $this->refrenceEntity($entities);
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

        $refrenceEntity = $this->refrenceEntity($entities);
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


    protected function refrenceEntity(array $entities)
    {

        $firstkey = array_key_first($entities);
        $refrenceEntity = $entities[$firstkey];
        return $refrenceEntity;
    }


    public function get($entities)
    {


        if (!is_array($entities)) $entities = [$entities];

        $refrenceEntity = $this->refrenceEntity($entities);

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

    public function search($data)
    {


        // $path = $this->path(['service', 'temp2'], ['user', 'temp']);

        // print_r($path);


        // exit;
        //each search will be selected and united in the query 


        foreach ($data as $searchKey => $search) {
            $endTables = [];
            $startTables = [];
            foreach ($search as $modelType => $values) {

                if ($modelType == "where") {

                    $orConditions = [];

                    foreach ($values as $orKey => $models) {

                        $andConditions = [];

                        foreach ($models as $model => $entities) {

                            $entityOrConditions = [];

                            foreach ($entities as $entityNumber => $entity) {

                                if ($entity->isValid()) {
                                    $endTables[] = $model;
                                    $entityOrConditions[] =  $this->querryAttrsSearch($entity->getAttrs(),  $searchKey, $model, $orKey, $entityNumber);
                                } else {
                                }
                            }
                            $andConditions[] = " ( " . implode(" OR ", $entityOrConditions) . " ) ";
                        }
                        $orConditions[] = " ( " . implode(" AND ", $andConditions) . " ) ";
                    }

                    $searchsConditions = implode(" OR ", $orConditions);
                } elseif ($modelType == "select") {

                    foreach ($values as $model => $entity) {


                        if ($entity->isValid()) {

                            // exit;
                            $startTables[] = $model;
                            if (!isset($entity->getAttrs()['columns'])) {
                                $selects[] = "$model.*";
                            } else {
                                $selects[] =  $this->querryAttrsSelect($entity->getAttrs()['columns'],  $model);
                            }
                        }
                    }
                }
            }



            // $endTables = array_unique($endTables);
            // print_r($endTables);
            // print_r($startTables);
            // exit;


            // select .....
            //table links
            $endTables = array_unique($endTables);

            $paths = $this->path($startTables, $endTables);

            //make the querry 
            $requete = [];
            foreach ($paths as $pathKey => $path) {
                $fromTable = $path[array_key_first($path)];

                unset($path[array_key_first($path)]);

                $joins = '';
                foreach ($path as $element) {
                    $ref = $element['ref'];
                    $table = $element['table'];
                    $key = $element['key'];
                    $refKey = $element['refKey'];

                    $joins .= " JOIN $ref ON $table.$key = $ref.$refKey ";
                }

                $select = $selects[$pathKey];


                $requete[] = "SELECT $select FROM $fromTable $joins GROUP BY $fromTable.id WHERE $searchsConditions";
            }
            print_r($requete);
        }
        exit;
    }
    protected function querryAttrsSearch($attrs, $searchKey, $tableName, $orKey, $entityNumber)
    {
        $queryAttrs = '';
        // print_r($attrs);
        // exit;

        foreach ($attrs as $key => $attr) {

            foreach ($attr as $keyy => $v) {
                $operator = $v["operator"];

                foreach ($v["values"] as $k => $value) {
                    // $queryAttrs .= " $tableName.$key $operator :$key$searchKey$orkey$keyy$k ";
                    $queryAttrs .= " $tableName.$key $operator :$searchKey$tableName$orKey$entityNumber$key$keyy$k ";
                    if (array_key_last($v["values"]) != $k)  $queryAttrs .= ' AND ';
                }
                if (array_key_last($attr) != $keyy)  $queryAttrs .= ' AND ';
            }

            if (array_key_last($attrs) != $key)  $queryAttrs .= ' AND ';
        }


        return "($queryAttrs)";
    }

    protected function querryAttrsSelect($attrs, $tableName)
    {

        if (empty($attrs)) return "$tableName.*";


        $queryAttrs = '';

        foreach ($attrs as $key => $attr) {

            $queryAttrs .= "$tableName.$attr";

            if (array_key_last($attrs) != $key)  $queryAttrs .= ' , ';
        }


        return $queryAttrs;
    }


    protected  function path(array $startTables, array $endTables)
    {
        $directLinksTable = '-';
        $result = [];

        foreach ($startTables as $key => $startTable) {

            foreach ($endTables as $endtable) {

                if ($startTable == $endtable) {

                    $result[$key][] = [$startTable];
                } else {

                    if ($directLinksTable == '-') $directLinksTable  = $this->directLinksTable();

                    $result[$key][] = $this->tableDirectLinks($startTable, $endtable, $directLinksTable, [$startTable], '');
                }
            }
        }

        foreach ($result as $key => $value) {
            $result[$key] = array_unique(array_merge(...$value), SORT_REGULAR);
        }


        return $result;
    }

    protected function directLinksTable()
    {
        //get each table direct links
        $db = 'radeeta';

        $requete = "    SELECT t.* FROM (
                      SELECT
                          TABLE_NAME as 'table',
                          COLUMN_NAME as 'column',
                          REFERENCED_TABLE_NAME as 'ref_table',
                          REFERENCED_COLUMN_NAME as 'ref_column'
                      FROM
                          information_schema.key_column_usage
                      WHERE
                          REFERENCED_TABLE_NAME IS NOT NULL
                          AND REFERENCED_COLUMN_NAME IS NOT NULL
                      UNION all
                      SELECT
                          REFERENCED_TABLE_NAME as 'table',
                          REFERENCED_COLUMN_NAME as 'column',
                          TABLE_NAME as 'ref_table',
                          COLUMN_NAME as 'ref_column'
                      FROM
                          INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE
                          REFERENCED_TABLE_SCHEMA = :db
                  ) as t
                  ORDER BY
                  t.table";


        $requete = $this->dao->prepare($requete);
        $requete->bindValue(":db", $db, \PDO::PARAM_STR);
        $requete->execute();

        $directLinks = [];
        if ($results = $requete->fetchAll()) {
            $directLinks = [];
            foreach ($results as $element) {
                $directLinks[$element['table']][] = $element;
            }
        }
        return $directLinks;
    }

    protected function tableDirectLinks($startingTable, $endTable, $directLinks, $path, $previousTable)
    {
        if (!isset($directLinks[$startingTable])) {
            return [];
        }

        $links = $directLinks[$startingTable];
        foreach ($links as $link) {


            $refTable =  $link['ref_table'];
            $tempPath = $path;
            $tempPath[] = ['table' => $startingTable, 'key' => $link['column'], 'ref' => $refTable, 'refKey' => $link['ref_column']];

            if ($refTable == $endTable) {
                return $tempPath;
            } elseif ($previousTable != $refTable) {
                // if ($refTable == 'user') echo 'yesss';
                // echo ' go ';
                $tempPath = $this->tableDirectLinks($refTable, $endTable, $directLinks, $tempPath, $startingTable);
                if ($tempPath != []) {
                    return $tempPath;
                }
            }
        }
        return [];
    }
}