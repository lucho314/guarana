<?php
include_once('html_sup.php');
include_once('lib/connect_mysql.php');
include_once('lib/funciones.php');
$inicio		= LimpiarXSS($_POST['fecha_inicial']);
$actual		= LimpiarXSS($_POST['fecha_final']);
$ente_id	= LimpiarXSS($_POST['ente_id']);
$cooperativa	= DevuelveValor($ente_id,'descripcion','ente_externos','id');
$listado_id	= LimpiarXSS($_POST['listado_id']);
$consulta 	= "SELECT * FROM listados WHERE id=$listado_id";
$resultado	= mysql_query($consulta,$link);
$fila		= mysql_fetch_row($resultado); 
$id_lista	= $fila[0];
$descripcion	= $fila[1];
$sql		= $fila[2];
$parametros	= $fila[3];
$sql 		= str_replace('$$actual',$actual,$sql);
$sql 		= str_replace('$$inicial',$inicio,$sql);
$sql 		= str_replace('$$ente',$ente_id,$sql);
echo '<strong><u>'.$descripcion.'</u></strong><br><br>';
echo 'Desde el&nbsp;<strong>'.$inicio.'</strong>&nbsp;hasta el&nbsp;<strong>'.$actual.'</strong><br><br>';
echo 'Cooperativa:&nbsp;<strong>'.$cooperativa.'</strong>';

$parametros = explode ("\n", $parametros); 


if ($parametros[0]=='tiempo_transcurrido')
{
//recorro el array

//calculo tiempo transcurrido

//imprimo resultados.

} //Fin if tiempo_transcurrido

?>
<?php

include_once('html_inf.php');
?>