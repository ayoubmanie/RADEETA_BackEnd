<?php

namespace Lib;

// use InvalidArgumentException;

trait ManagerPDO
{

    protected $invalidEntities = [];
    protected $validEntitiesResult = [];


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


    public function response()
    {
        //add the http status if there is an invalidEntities!!!

        //don't use array_merge , it causes reindexing
        $array = $this->invalidEntities + $this->validEntitiesResult;


        return (object)$array;
    }

    public function search(array $data)
    {


        //each search will be selected and united in the query 
        $results = [];

        //check if there is at least one invali
        $validSearch = true;
        foreach ($data["objects"] as $searchKey => $search) {

            foreach ($data["objects"] as $searchKey => $search) {

                foreach ($search["where"] as $object) {

                    if (!$object->isValid()) $validSearch = false;
                }
            }

            if ($validSearch) {
                $conditions = [];
                foreach ($data["objects"] as $searchKey => $search) {

                    $endTables = [];

                    foreach ($search["where"] as $object) {

                        $model = strtolower($this->tableName($object));
                        $endTables[] = $model;

                        $conditions =  $conditions  + $object->getAttrs();
                    }

                    $startTables = [];
                    foreach ($search["select"] as $object) {

                        $model = strtolower($this->tableName($object));
                        $startTables[] = $model;

                        $selects[] =  $this->querryAttrsSelect($object->getAttrs(),  $model);
                    }
                }

                $conditions =  $conditions  + $data["logicOperator"];

                $requestCondition = $this->linkDataTree($conditions, $data["dataTree"]);


                // select .....
                //table links
                $endTables = array_unique($endTables); // not nessecary 

                $paths = $this->path($startTables, $endTables);


                //make the querry 
                $requetes = [];


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


                    $requetes[$fromTable] = "SELECT $select FROM $fromTable $joins WHERE $requestCondition GROUP BY $fromTable.id ";
                }

                foreach ($requetes as $selectKey => $requete) {

                    $requete = $this->dao->prepare($requete);

                    $requete = $this->bindsearchAttrs($requete, $conditions);

                    $requete->execute();

                    if ($result = $requete->fetchAll()) {

                        $results[$searchKey][$selectKey] = $result;
                        $this->validEntitiesResult[$searchKey][$selectKey] = $result;
                    }
                }
            } else {
                // send back the error in the dataTree with the specific index
            }
        }
    }

    protected function linkDataTree($data, $dataTree, $request = '')
    {
        $request .= " ( ";
        foreach ($dataTree as $index) {

            if (!is_array($index)) {


                $element = $data[$index];

                if (in_array($element, ["and", "or"])) {
                    $request .= " $element ";
                } else {
                    $table = $element["table"];
                    $column = $element["column"];
                    $operator = $element["operator"];
                    $request .= " `$table`.$column $operator :$index ";
                }
            } else {

                $request .= $this->linkDataTree($data, $index);
            }
        }
        $request .= " ) ";
        return $request;
    }

    protected function bindsearchAttrs($requete, $conditions)
    {
        foreach ($conditions as $index => $element) {

            if (isset($element["type"])) {
                $type = $element["type"];


                if ($type == 'integer') {

                    $requete->bindValue(":$index", $element["value"], \PDO::PARAM_INT);
                } elseif ($type == 'string') {

                    $requete->bindValue(":$index", $element["value"], \PDO::PARAM_STR);
                } else {
                    throw new \InvalidArgumentException("attribute type not specified in the class : '" . $element["table"] . "'");
                }
            }
        }

        return $requete;
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
        // exit;
        $links = $directLinks[$startingTable];
        foreach ($links as $link) {


            $refTable =  $link['ref_table'];
            $tempPath = $path;
            $tempPath[] = ['table' => $startingTable, 'key' => $link['column'], 'ref' => $refTable, 'refKey' => $link['ref_column']];

            if ($refTable == $endTable) {
                return $tempPath;
            } elseif ($previousTable != $refTable) {

                $tempPath = $this->tableDirectLinks($refTable, $endTable, $directLinks, $tempPath, $startingTable);
                if ($tempPath != []) {
                    return $tempPath;
                }
            }
        }
        return [];
    }
}