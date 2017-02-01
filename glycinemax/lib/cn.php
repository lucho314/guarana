<?php 
include_once("connect_mysql.php");
$link = mysql_connect($Servidor,$Usuario,$Clave)

        or die("Could not connect");

$db = mysql_select_db($NombreDB, $link)
		or die("Could not select database");
?>