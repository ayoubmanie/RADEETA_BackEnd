<?php

namespace Lib;

// use InvalidArgumentException;

trait ManagerPDO
{

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
        foreach ($entities as $key => $entity) {

            if ($entity->isValid()) {
                //remove the id
                $attrs = array_diff($entity->updateAttrs(), [$id]);


                foreach ($attrs as $attr) {
                    $allUpdateAttrs[$attr][] = "WHEN id = :id$key THEN :$attr$key";
                }

                $ids .= ":id$key";

                if (array_key_last($entities) != $key)  $ids .= ' , ';
            }
        }

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
        foreach ($entities as $key => $entity) {
            $requete = $this->bindAllAttrs($requete, $entity, $entity->updateAttrs(), $key);
        }

        // execute;
        $requete->execute();
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
        foreach ($entities as $key => $entity) {
            if ($entity->isValid()) {
                $queryAddAttrs .= $this->querryAttrsAdd($refrenceEntity->addAttrs(), $key);
            }
            if (array_key_last($entities) != $key)  $queryAddAttrs .= ', ';
        }

        // query
        $requete = "INSERT INTO $tableName ($queryColumns) VALUES $queryAddAttrs";


        // prepare
        $requete = $this->dao->prepare($requete);

        // bind 
        foreach ($entities as $key => $entity) {
            $requete = $this->bindAllAttrs($requete, $entity, $refrenceEntity->addAttrs(), $key);
        }
        // execute
        $requete->execute();
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


    protected function bindAllAttrs($requete, $entity, $attrs, $key)
    {
        foreach ($attrs as $attr) {
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
}