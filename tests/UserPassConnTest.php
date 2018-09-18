<?php

class UserPassConnTest extends PHPUnit_Framework_TestCase
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
                //'database' => 'garri'
            ));

            $this->d();
            $this->cc();
        } catch (\Exception $e) {
            $this->assertEquals("3D000", $e->getCode());
//            print $e->getMessage();
//            exit ();
        }

        // var_dump($this->pdo);
    }

    public function d()
    {
        try {
            $this->pdo->exec('DROP TABLE IF EXISTS `admin`');
        } catch (Exception $e) {
            $this->assertEquals("3D000", $e->getCode());
//            print $e->getMessage();
        }
    }

    public function testConnect()
    {
        $this->c();
        try {
            $this->ii();
        } catch (Exception $exception) {
            $this->assertEquals("3D000", $exception->getCode());
        }
        $this->pdo->useDb("garri");
        $this->d();
        $this->cc();
        $this->ii();
    }

    private function ii()
    {
        $this->pdo->exec("INSERT INTO `admin`(
          `name`,
          `pass`,
          `real_name`,
          `pid`,
          `role`,
          `status`
          
          )
                VALUES (
                  'name','pass','real','0','role','1'
                
                )"
        );
    }

    private function cc()
    {
        $this->pdo->exec("
			CREATE TABLE `admin` (
              `admin_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(32) DEFAULT NULL,
              `pass` VARCHAR(32) DEFAULT '',
              `real_name` VARCHAR(32) DEFAULT NULL,
              `pid` INT(10) UNSIGNED DEFAULT NULL COMMENT 'editor的operator',
              `role` ENUM('admin','operator','editor') DEFAULT NULL,
              `status` ENUM('normal','block') DEFAULT 'normal',
              `date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`admin_id`),
              UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

		");
    }
}


