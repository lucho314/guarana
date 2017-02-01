<?php
$tip = '';
$afiliado = true;
include_once('html_sup.php');
include("scaffold.php");

$acciones = ['D', 'E','','N'];
$filter = [];
echo $_SESSION['usuario_nivel'];
if ($_SESSION['usuario_nivel'] == 2) {
    $filter = ['afiliado_id', $_SESSION['afiliado_id']];
   $acciones[2]='';
}

new Scaffold("editable", "reservas", 30, array('salon_id', 'turno_id', 'afiliado_id', 'descripcion', 'importe', 'pagado_id'), array(), // Campos a ocultar en el formulario
        array(), // Campos relacionados
        array(), // Campos a ocultar del maestro en el detalle
        $acciones, $filter
);
include_once('html_inf.php');
?>
<button onclick="validacion()">presionar</button>