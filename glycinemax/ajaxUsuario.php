<?php

include_once('lib/connect_mysql.php');
include_once('lib/funciones.php');
$afiliadoId = $_POST['afiliadoId'];
$funcion = $_POST['funcion'];
echo $funcion($afiliadoId);

function checkExist() {
    $sql = "SELECT * from usuarios where usuario='" . $_POST['usuario'] . "' or  descripcion like '%" . $_POST['cuit'] . "'";
    $result = mysql_query($sql);
    return json_encode(mysql_fetch_array($result));
}
