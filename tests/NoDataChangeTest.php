<?php

class NoDataChangeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Aw\Db\Connection\Mysql $pdo
     */
    private $pdo;

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
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

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

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testInsert2()
    {
        $this->c();
        // 不是AUTO的PK，返回为LAST_INSERT_ID为0，所以这里使用EXEC，返回影响行数更合理
        $id = $this->pdo->insert("
				INSERT INTO `garri`.`admin` (
				  `name`,
				  `pass`
				)
				VALUES
				  (

				    :name,
				    :pass
				  );

			", array(
            'name' => 'root',
            'pass' => '2017-5-27'
        ));
        $this->assertEquals($id, 1);
        $id = $this->pdo->exec("UPDATE `garri`.`admin` SET `name` = 'root' WHERE admin_id = 1");
        $this->assertTrue($this->pdo->isNoDataUpdate());
        $this->assertEquals($id, 0);
        $this->pdo->exec("UPDATE `garri`.`admin` SET `name` = 'root' WHERE admin_id = 11");
        $this->assertTrue($this->pdo->isNoDataUpdate());
//        $this->pdo->exec("UPDATE `garri`.`admin` SET `namex` = 'root' WHERE admin_id = 11");
//        $this->assertTrue($this->pdo->isNoDataUpdate());
        $this->d();
    }
}


