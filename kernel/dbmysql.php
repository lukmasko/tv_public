<?php
namespace Kernel\DB;

use Kernel\Config;

class DbMySql extends \PDO
{
    private static $dbh;
    private static $instance;

    private function __construct(){
        parent::__construct(Config\Config::$dsn, Config\Config::$user, Config\Config::$pass, Config\Config::$options);
    }

    public static function getInstance() : DbMySql {
        if(self::$instance === null){
            self::$instance = new static();
        }
        return self::$instance;
    }
}