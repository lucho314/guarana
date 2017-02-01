<?php
$tip = '';
$afiliado=true;
include_once('html_sup.php');
include("scaffold.php");

$acciones=['D','E','B','N'];
$filter=[];

if($_SESSION['usuario_nivel']==2)
{
    $filter=['afiliado_id',$_SESSION['afiliado_id']];
    $acciones=array_slice($acciones, 0,2);
}


new Scaffold("editable","familiars",
        30,
        array('afiliado_id','parentesco_id','nombre','apellido'),
        array(),               // Campos a ocultar en el formulario
        array(),                                                         // Campos relacionados
        array(),                                                          // Campos a ocultar del maestro en el detalle
        $acciones,
        $filter
        );
include_once('html_inf.php');
?>