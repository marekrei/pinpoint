<?php
$db = mysql_connect($mysql_host, $mysql_user, $mysql_pass);
if(!$db){
	die("Unable to connect to database");
}
mysql_select_db($mysql_db, $db);
mysql_query("SET NAMES 'utf8'");
?>