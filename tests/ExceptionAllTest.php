<?php

class ExceptionAllTest extends PHPUnit_Framework_TestCase
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
            $this->pdo->setSilentMode();
            $this->pdo->exec("
                CREATE TABLE `ggg` (
                  `g_id` int(11) NOT NULL AUTO_INCREMENT,
                  `uni` varchar(8) DEFAULT NULL,
                  PRIMARY KEY (`g_id`),
                  UNIQUE KEY `uni` (`uni`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
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
            $this->pdo->exec('DROP TABLE IF EXISTS `ggg`');
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
    public function testInsert1()
    {
        $this->c();
        // 不是AUTO的PK，返回为LAST_INSERT_ID为0，所以这里使用EXEC，返回影响行数更合理
        $id = $this->pdo->exec("
				INSERT INTO `ggg` (
				  `uni`
				)
				VALUES
				  (
				    :uni
				  );

			", array(
            'uni' => 'lol'
        ));
        $this->assertEquals($id, 1);
        $id = $this->pdo->exec("
				INSERT INTO `ggg` (
				  `uni`
				)
				VALUES
				  (
				    :uni
				  );

			", array(
            'uni' => 'lol'
        ));
        $this->assertEquals($id, null);
        $this->assertTrue($this->pdo->isDuplicateEntry());
        $this->d();
    }
}


