<?php

/**
 * $links->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
 * 设置为SILENT模式，执行有问题会有WARNNING产生
 */
namespace Tian\Connection;

use PDO;
use Exception;

class MysqlPdoConn implements IConnection {
	// 查询语句日志
	protected static $queryLogs = [ ];
	protected $config;
	
	/**
	 *
	 * @var \PDO
	 */
	protected $pdo;
	/**
	 * 获取连接
	 *
	 * @param array $config
	 *        	[
	 *        	'host' => 127.0.0.1,
	 *        	'port' => 3306,
	 *        	'database' => db,
	 *        	'user' => user
	 *        	'password' => pass
	 *        	'charset' => utf8
	 *        	]
	 *        	
	 */
	public function __construct(array $config) {
		$this->config = $config;
		$this->chkConf ();
		$dns = 'mysql:host=' . $this->getHost () . ';port=' . $this->getPort () . ';dbname=' . $this->getDbName ();
		$links = new PDO ( $dns, $config ['user'], $config ['password'], [ 
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $this->getCharset () . "'" 
		] );
		// $links->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
		// if (isset ( $config ['silent'] ) && $config ['silent']) {
		// $links->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
		// } else {
		// $links->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		// }
		$links->exec ( "SET sql_mode = ''" );
		$this->pdo = $links;
	}
	private function chkConf() {
		$f = true;
		$f = $f && is_array ( $this->config );
		$f = $f && array_key_exists ( 'host', $this->config );
		$f = $f && array_key_exists ( 'port', $this->config );
		$f = $f && array_key_exists ( 'database', $this->config );
		$f = $f && array_key_exists ( 'user', $this->config );
		$f = $f && array_key_exists ( 'password', $this->config );
		$f = $f && array_key_exists ( 'charset', $this->config );
		if (! $f) {
			throw new \Exception ( 'Malformed config.' . var_export ( $this->config, true ) . ' --- 
				host => 127.0.0.1,
				port => 3306,
				database => db,
				user => user
				password => pass
				charset => utf8
				' );
		}
	}
	public function getDbName() {
		return $this->config ['database'];
	}
	public function getHost() {
		return $this->config ['host'];
	}
	public function getPort() {
		return $this->config ['port'];
	}
	public function getCharset() {
		return $this->config ['charset'];
	}
	/**
	 *
	 * 返回插入ID
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @param array $bindType
	 *        	KEY和DATA一样，值为PDO:PARAM_**
	 * @return int
	 */
	public function insert($sql, $data = [], $bindType = []) {
		self::$queryLogs [] = $sql . " " . var_export ( $data, true );
		$sth = $this->pdo->prepare ( $sql );
		if (! $sth) {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return 0;
		}
		foreach ( $data as $k => $v ) {
			$sth->bindValue ( $k, $v, array_key_exists ( $k, $bindType ) ? $bindType [$k] : \PDO::PARAM_STR );
		}
		if (@$sth->execute ()) {
			$id = $this->pdo->lastInsertId ();
			return $id;
		} else {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return 0;
		}
	}
	/**
	 *
	 * 返回一维数组,SQL中的结果集中的第一个元组
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @return array;
	 */
	public function fetch($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC) {
		$sth = $this->pdo->prepare ( $sql );
		self::$queryLogs [] = $sql . " " . var_export ( $data, true );
		if (! $sth) {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return [ ];
		}
		foreach ( $data as $k => $v ) {
			$sth->bindValue ( $k, $v, array_key_exists ( $k, $bindType ) ? $bindType [$k] : \PDO::PARAM_STR );
		}
		$sth->setFetchMode ( $fetch_mode );
		if (@$sth->execute ()) {
			$ret = $sth->fetch ();
			if (! is_array ( $ret ))
				return [ ];
			return $ret;
		}
		$error = $sth->errorInfo ();
		throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
		return 0;
	}
	
	/**
	 *
	 * 返回二维数组
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @return array;
	 */
	public function fetchAll($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC) {
		$sth = $this->pdo->prepare ( $sql );
		self::$queryLogs [] = $sql . " " . var_export ( $data, true );
		if (! $sth) {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return [ ];
		}
		foreach ( $data as $k => $v ) {
			$sth->bindValue ( $k, $v, array_key_exists ( $k, $bindType ) ? $bindType [$k] : \PDO::PARAM_STR );
		}
		$sth->setFetchMode ( $fetch_mode );
		if (@$sth->execute ()) {
			$r = $sth->fetchAll ();
			return $r;
		}
		$error = $sth->errorInfo ();
		throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
		return [ ];
	}
	/**
	 *
	 * 返回影响行数
	 *
	 * @param string $sql        	
	 * @param array $data        	
	 * @param array $bindType
	 *        	KEY和DATA一样，值为PDO:PARAM_**
	 * @return int
	 */
	public function exec($sql, $data = [], $bindType = []) {
		$sth = $this->pdo->prepare ( $sql );
		if (! $sth) {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return 0;
		}
		foreach ( $data as $k => $v ) {
			$sth->bindValue ( $k, $v, array_key_exists ( $k, $bindType ) ? $bindType [$k] : \PDO::PARAM_STR );
		}
		if (@$sth->execute ()) {
			return $sth->rowCount ();
		} else {
			$error = $sth->errorInfo ();
			throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
			return 0;
		}
	}
	// /**
	// * 批量添加
	// * 返回影响行数
	// *
	// * @param string $table
	// * @param array $fileds,里面加了`也没有关系
	// * @param array $data二维数组，使用数字索引
	// * [
	// * [v1,v3,v4],
	// * ...
	// * ]
	// * @param array $bindType
	// * KEY和$fileds一样,如果field带了`符号，以去掉·为准，值为PDO:PARAM_**
	// * @return int
	// */
	// public function batchInsert($table, array $fileds, $data = [], $bindType = []) {
	// $sql = "INSERT INTO TABLE (FIELD) VALUES PLACEHOLDER";
	
	// // 参数检查
	// if (count ( $data ) && ! is_array ( $data [0] ))
	// throw new \Exception ( "Malformed data" . var_export ( $data, true ) . " --- [[v1,v3,v5],[vw1,ve3,vwe5],...] " );
	
	// $fileds = array_map ( function ($item) {
	// return str_replace ( '`', '', $item );
	// }, $fileds );
	// $filed = implode(',', $fileds);
	// $sth = $this->pdo->prepare ( $sql );
	// if (! $sth) {
	// $error = $sth->errorInfo ();
	// throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
	// return 0;
	// }
	// foreach ( $data as $k => $v ) {
	// $sth->bindValue ( $k, $v, array_key_exists ( $k, $bindType ) ? $bindType [$k] : \PDO::PARAM_STR );
	// }
	// if ($sth->execute ()) {
	// return $sth->rowCount ();
	// } else {
	// $error = $sth->errorInfo ();
	// throw new Exception ( $sql . " ;BindParams:" . var_export ( $data, true ) . implode ( ';', $error ) );
	// return 0;
	// }
	// }
	/**
	 * 执行事务处理
	 *
	 * @param \Closure $closure        	
	 *
	 * @return $this
	 */
	public function transaction(\Closure $closure) {
		try {
			$this->beginTransaction ();
			// 执行事务
			$closure ();
			$this->commit ();
		} catch ( Exception $e ) {
			// 回滚事务
			$this->rollback ();
		}
		return $this;
	}
	/**
	 * 开启一个事务
	 *
	 * @return $this
	 */
	public function beginTransaction() {
		$this->pdo->beginTransaction ();
		return $this;
	}
	/**
	 * 开启事务
	 *
	 * @return $this
	 */
	public function rollback() {
		$this->pdo->rollback ();
		return $this;
	}
	/**
	 * 开启事务
	 *
	 * @return $this
	 */
	public function commit() {
		$this->pdo->commit ();
		return $this;
	}
	/**
	 * 获得查询SQL语句
	 *
	 * @return array
	 */
	public function getQueryLog() {
		return self::$queryLogs;
	}
}