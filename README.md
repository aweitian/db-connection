# 配置

PDO Connection组件

##开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用。
>composer require aweitian/dbConnection
>
```
new Tian\Connection\MysqlPdoConn ( [ 
	'host' => '127.0.0.1',
	'port' => 3306,
	'user' => 'root',
	'password' => 'root',
	'charset' => 'utf8',
	'database' => 'garri' 
] );
getDbName();
getHost();
getCharset();
insert($sql, $data = [], $bindType = []);
fetch($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC);
fetchAll($sql, $data = [], $bindType = [], $fetch_mode = \PDO::FETCH_ASSOC);
exec($sql, $data = [], $bindType = []);
transaction(\Closure $closure);
beginTransaction();
rollback();
commit();
getQueryLog(); 静态访问
```