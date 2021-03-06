<?php

class PdoTest extends PHPUnit_Framework_TestCase
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
			CREATE TABLE `gg` (
			  `pk1` INT(10) UNSIGNED NOT NULL,
			  `pk2` INT(10) UNSIGNED NOT NULL,
			  `fint` INT(10) UNSIGNED DEFAULT '16',
			  `data` VARCHAR(10) DEFAULT NULL,
			  `fenum` ENUM('aa','bb') DEFAULT NULL COMMENT 'comment_enum',
			  `fset` SET('a','bc') DEFAULT NULL,
			  `notnullable` TEXT NOT NULL,
			  PRIMARY KEY (`pk1`,`pk2`),
			  UNIQUE KEY `fint` (`fint`)
			) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='gg_comment';

			CREATE TABLE `g` (
			  `pk1` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `fint` int(10) unsigned DEFAULT '16',
			  `data` varchar(10) DEFAULT NULL,
			  PRIMARY KEY (`pk1`),
			  UNIQUE KEY `fint` (`fint`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='gg_comment'

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
            $this->pdo->exec('DROP TABLE IF EXISTS `g`');
            $this->pdo->exec('DROP TABLE IF EXISTS `gg`');
        } catch (Exception $e) {
            print $e->getMessage();
        }

    }

    public function testConnect()
    {
        $this->assertEquals('127.0.0.1', '127.0.0.1');
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws Exception
     */
    public function testInsert1()
    {
        $this->c();
        // 不是AUTO的PK，返回为LAST_INSERT_ID为0，所以这里使用EXEC，返回影响行数更合理
        $id = $this->pdo->exec("
				INSERT INTO `garri`.`gg` (
				  `pk1`,
				  `pk2`,
				  `fint`,
				  `data`,
				  `fenum`,
				  `fset`,
				  `notnullable`
				)
				VALUES
				  (
				    :pk1,
				    :pk2,
				    :fint,
				    :data,
				    :fenum,
				    :fset,
				    :notnullable
				  );

			", array(
            'pk1' => 1,
            'pk2' => 2,
            'fint' => 22,
            'data' => '2017-5-27',
            'fenum' => 'aa',
            'fset' => 'a,bc',
            'notnullable' => 'lol'
        ));
        $this->assertEquals($id, 1);
        $this->d();
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
				INSERT INTO `garri`.`g` (
				  `fint`,
				  `data`
				)
				VALUES
				  (

				    :fint,
				    :data
				  );

			", array(
            'fint' => 22,
            'data' => '2017-5-27'
        ));
        $this->assertEquals($id, 1);
        $id = $this->pdo->insert("
				INSERT INTO `garri`.`g` (
				  `fint`,
				  `data`
				)
				VALUES
				  (

				    :fint,
				    :data
				  );

			", array(
            'fint' => 5,
            'data' => '2017-1-27'
        ));
        $this->assertEquals($id, 2);
        $this->d();
    }
//
//    public function testFetch()
//    {
//        $this->c();
//        // 不是AUTO的PK，返回为LAST_INSERT_ID为0，所以这里使用EXEC，返回影响行数更合理
//        $this->testInsert2();
//        $row = $this->pdo->fetch("SELECT * FROM `garri`.`g`");
//        $this->assertArraySubset(array(
//            'fint' => 22,
//            'data' => '2017-5-27'
//        ), $row);
//        $this->d();
//    }
//
//    public function testFetchScalar()
//    {
//        $this->c();
//        $this->testInsert2();
//        $value = $this->pdo->scalar("SELECT fint FROM `garri`.`g`");
//        $this->assertEquals(5, $value);
//        $this->d();
//    }
//
//    public function testFetchAll()
//    {
//        $this->c();
//        // 不是AUTO的PK，返回为LAST_INSERT_ID为0，所以这里使用EXEC，返回影响行数更合理
//        $this->testInsert2();
//        $data = $this->pdo->fetchAll("SELECT * FROM `garri`.`g`");
//        $this->assertEquals(2, count($data));
//        $this->d();
//    }
//
//    public function testDelete()
//    {
//        $this->c();
//        $this->testInsert2();
//        $data = $this->pdo->fetchAll("SELECT * FROM `garri`.`g`");
//        $this->assertEquals(2, count($data));
//        $this->pdo->exec("Delete from g");
//        $data = $this->pdo->fetchAll("SELECT * FROM `garri`.`g`");
//        $this->assertEquals(0, count($data));
//        $this->d();
//    }
//
//    public function testUpdate()
//    {
//        $this->c();
//        $this->testInsert2();
//        $cnt = $this->pdo->exec("UPDATE g SET `fint` = `fint` + 1");
//        $this->assertEquals(2, $cnt);
//        $data = $this->pdo->fetch("SELECT * FROM `garri`.`g`");
//        $this->assertEquals(23, $data['fint']);
//        $this->d();
//    }
//
//    public function testExecuteExcetpion()
//    {
//        //$this->pdo->exec( "UPDATE g SET `fint` = `fint` + :a",['aa'=>2] );
//    }
}


