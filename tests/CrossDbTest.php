<?php

class CrossDbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Aw\Db\Connection\Mysql $pdo
     */
    private $pdo;
    /**
     * @var Aw\Db\Connection\Mysql $pdo
     */
    private $pdo_sync;
    public function c()
    {
        try {
            $this->pdo = new Aw\Db\Connection\Mysql (array(
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'database' => 'garri'
            ));
            $this->pdo_sync = new Aw\Db\Connection\Mysql (array(
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'database' => 'm11'
            ));
            $this->d();
            $this->pdo->exec("
			CREATE TABLE `admin` (
              `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(32) DEFAULT NULL,
              `pass` varchar(32) DEFAULT '',
              `real_name` varchar(32) DEFAULT NULL,
              `pid` int(10) unsigned DEFAULT NULL COMMENT 'editor的operator',
              `role` enum('admin','operator','editor') DEFAULT NULL,
              `status` enum('normal','block') DEFAULT 'normal',
              `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`admin_id`),
              UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

		");
        } catch (\Exception $e) {
            print $e->getMessage();
            exit ();
        }

        // var_dump($this->pdo);
    }

    public function d()
    {
        try {
            $this->pdo->exec('DROP TABLE IF EXISTS `admin`');
        } catch (Exception $e) {
            print $e->getMessage();
        }

    }

    public function testConnect()
    {
        $this->c();
        $this->pdo->exec("INSERT INTO `garri`.`admin`(
          `admin_id`,
          `name`,
          `pass`,
          `real_name`,
          `pid`,
          `role`,
          `status`,
          `date`
          
          )
                SELECT
                `admin_id`,
                  `name`,
                  `pass`,
                  `real_name`,
                  `pid`,
                  `role`,
                  `status`,
                  `date`
                FROM `m11`.`admin` ");
    }
}


