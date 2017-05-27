<?php
/**
 * @date 2017/5/16 18:37:26
 * 
 * 
 **/
namespace Tian\Connection;
interface IConnection
{
	function getDbName();
	function getHost();
	function getCharset();
	function insert($sql, $data = [], $bindType = []);
	function fetch($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC);
	function fetchAll($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC);
	function exec($sql, $data = [], $bindType = []);
	function transaction(\Closure $closure);
	function beginTransaction();
	function rollback();
	function commit();
	function getQueryLog();
}