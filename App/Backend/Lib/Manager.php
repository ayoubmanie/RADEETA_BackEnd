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
}