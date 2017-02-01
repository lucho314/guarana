<?php

include_once('lib/connect_mysql.php');
include_once('lib/funciones.php');
$afiliadoId = $_POST['afiliadoId'];
$funcion = $_POST['funcion'];
echo $funcion($afiliadoId);

function getCelular($afiliadoId) {
    $sqlCelularAfiliado = "SELECT id, nro_telefono FROM celulars WHERE afiliado_id=$afiliadoId";
    $celularesAfiliado = mysql_query($sqlCelularAfiliado);
    $option = "<option> Seleccionar celular </option>";
    while ($celular = mysql_fetch_array($celularesAfiliado)) {
        $option .= "<option value='$celular[0]'> $celular[1]</option>";
    }
    return $option;
}

function checkExist() {
    $cuit = $_POST['cuit'];
    $sql = "SELECT * FROM afiliados WHERE cuil = '$cuit'";
    $result = mysql_query($sql);
    return json_encode(mysql_fetch_array($result));
}
