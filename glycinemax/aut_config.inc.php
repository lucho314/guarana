<?php
// Configuraci�n
include_once("lib/connect_mysql.php");
// Nombre de la session (puede dejar este mismo)
$usuarios_sesion="Arabidopsis";

// Datos conexi�n a la Base de datos (MySql)
$sql_host       = $Servidor;  // Host, nombre del servidor o IP del servidor Mysql.
$sql_usuario    = $Usuario;        // Usuario de Mysql
$sql_pass       = $Clave;           // contrase�a de Mysql
$sql_db         = $NombreDB;     // Base de datos que se usar�.
$sql_tabla      = "usuarios"; // Nombre de la tabla que contendr� los datos de los usuarios
?>
