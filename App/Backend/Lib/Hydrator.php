<?php

namespace Lib;

trait Hydrator
{

    public function hydrate($entity, $data, $action)
    {

        foreach ($entity as $key => $value) {

            if ($action == 'update') {
                $this->updateAttrs = array_keys($entity);
            }

            if (array_key_exists($key, $data)) {
                $method = 'set' . ucfirst($key);
            } else {
                $this->erreurs[$key] = "missing";
                continue;
                // throw new \InvalidArgumentException("Post data '" . $key . "' is missing");
            }


            if (is_callable([$this, $method])) {
                $this->$method($data[$key]);
            } else {
                unset($entity[$key]);
            }
            // else {
            //     throw new \Exception("Method '" . $method . "' must be declared");
            // }
        }

        if ($action == 'update') {
            $this->updateAttrs = array_keys($entity);
        }
    }
}