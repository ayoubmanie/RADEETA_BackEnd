<?php

namespace Lib;

class PDOFactory
{
    public static function getMysqlConnexion()
    {
        $dsn = "mysql:host=localhost;dbname=radeeta";
        $options = [
            \PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions for php 8 and more, it's set by default
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];

        $db = new \PDO($dsn, "root", "", $options);
        return $db;
    }
}