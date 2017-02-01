<?php
$tip = '';
include_once('html_sup.php');
include("scaffold.php");

new Scaffold("editable","reservas",
        30,
        array('salon_id','turno_id','afiliado_id','descripcion','importe','pagado_id'),
        array(),               // Campos a ocultar en el formulario
        array(),                                                         // Campos relacionados
        array(),                                                          // Campos a ocultar del maestro en el detalle
        array('D','E','B','N')
        );
include_once('html_inf.php');
?>
<button onclick="validacion()">presionar</button>