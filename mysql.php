<?php
$mysql_server_name='localhost';
$mysql_username=‘xxxxxx’;
$mysql_password=‘xxxxxxx’;
$mysql_database=‘xxxxxxxx’;

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);
?>