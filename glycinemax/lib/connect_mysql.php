<?php
$Servidor = "localhost";
$Usuario = "root";
$Clave = ""; 
$NombreDB = "cae";
mysql_pconnect($Servidor, $Usuario, $Clave) or die("Error al conectar a mysql");
mysql_select_db($NombreDB) or die("Error al seleccionar base de datos");
$link=mysql_pconnect($Servidor, $Usuario, $Clave) or die("Error al conectar a mysql");
?>