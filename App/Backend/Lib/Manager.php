<?php

namespace lib;

abstract class Manager
{
    protected $dao;
    protected Entity $entity;

    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    // public function update(Entity $test)
    // {
    //     $this->entity = $test;
    //     $className = array_values(explode("\\", get_class($test))); //do not remove ! (to avoid a warning)
    //     $tableName = end($className);

    //     $requeteSql = "UPDATE $tableName SET ";


    //     $ids  = $this->entity->classId();
    //     if (!is_array($ids)) $ids = [$ids]; //to unify, because there is some entities that has an array of ids

    //     $attrs = array_diff($test->attrsToInsert(), $ids);

    //     // form the request 
    //     foreach ($attrs as $key => $value) {
    //         $requeteSql .= $value . ' = :' . $value . ' ';
    //         if (array_key_last($attrs) != $key)  $requeteSql .= ', ';
    //     }
    //     $requeteSql .= " WHERE ";
    //     foreach ($ids as $key => $value) {
    //         $requeteSql .= " $value = :$value";
    //         if (array_key_last($ids) != $key)  $requeteSql .= ' AND ';
    //     }


    //     $this->requete = $this->dao->prepare($requeteSql);
    //     foreach ($test->attrsToInsert() as $key => $attr) {
    //         $method = 'bind' . ucfirst($attr);

    //         if (is_callable([$this, $method])) {
    //             $this->$method();
    //         } else {
    //             throw new \Exception("Method '" . $method . "' must be declared");
    //         }
    //     }

    //     $this->requete->execute();
    // }
}