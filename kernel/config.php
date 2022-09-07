<?php

namespace Kernel\Config;

class Config
{
    public static $dsn = 'mysql:host=localhost;dbname=tv';
    public static $user = 'root';
    public static $pass = '';
    public static $options = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
                            /*\PDO::ATTR_ERRMODE => \PDO::ERRMODE_SILENT*/);

}