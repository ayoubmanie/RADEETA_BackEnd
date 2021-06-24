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

    public function update(Entity $entity)
    {

        if ($entity->isValid()) {

            $tableName = $this->tableName($entity);

            $id  = $entity->classId();
            $attrs = array_diff($entity->updateAttrs(), [$id]);
            $queryUpdateAttrs = $this->querryAttrs($attrs);


            // This was used when the class had mutiple keys 
            // $queryIds = "";
            // foreach ($ids as $key => $value) {
            //     $queryIds .= " $value = :$value";
            //     if (array_key_last($ids) != $key)  $queryIds .= ' AND ';
            // }
            $requete = "UPDATE $tableName SET $queryUpdateAttrs WHERE $id = :id";
            //might be used ! (no)
            // if (is_callable([$this, 'checkUpdate'])) {
            //     $this->checkUpdate($requete);
            // }

            $requete = $this->dao->prepare($requete);
            $requete = $this->bindAllAttrs($requete, $entity, $entity->updateAttrs());
            $requete->execute();
        }
    }


    public function add(Entity $entity)
    {
        if ($entity->isValid()) {
            $tableName = $this->tableName($entity);
            $queryAddAttrs = $this->querryAttrs($entity->addAttrs());

            $requete = "INSERT INTO $tableName SET $queryAddAttrs";

            $requete = $this->dao->prepare($requete);
            $requete = $this->bindAllAttrs($requete, $entity, $entity->addAttrs());
            $requete->execute();
        }
    }


    protected function tableName(Entity $entity)
    {
        $className = array_values(explode("\\", get_class($entity))); //do not remove ! (to avoid a warning)
        return  end($className);
    }

    protected function querryAttrs($attrs)
    {
        $queryAttrs = '';
        foreach ($attrs as $key => $value) {
            $queryAttrs .= $value . ' = :' . $value . ' ';
            if (array_key_last($attrs) != $key)  $queryAttrs .= ', ';
        }
        return $queryAttrs;
    }


    protected function bindAllAttrs($requete, $entity, $attrs)
    {
        foreach ($attrs as $attr) {
            $type = gettype($entity->$attr());

            if ($type == 'integer') {
                $requete->bindValue(":$attr", $entity->$attr(), \PDO::PARAM_INT);
            } elseif ($type == 'string') {
                $requete->bindValue(":$attr", $entity->$attr(), \PDO::PARAM_STR);
            } else {
                throw new \InvalidArgumentException("attribute type not specified in the class : '" . get_class($this->entity)) . "'";
            }
        }
        return $requete;
    }
}