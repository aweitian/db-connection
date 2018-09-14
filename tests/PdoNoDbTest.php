<?php

class PdoNoDbTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $dsn = 'mysql:host=127.0.0.1;port=3306;';
        $links = new PDO ($dsn, 'root', 'root');
        $links->exec("SET sql_mode = ''");
    }
}


