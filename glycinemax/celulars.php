<?php
$tip = '';
include_once('html_sup.php');
include("scaffold.php");

new Scaffold("editable","celulars",
        30,
        array('nro_telefono','imei','afiliado_id'),
        array(),               // Campos a ocultar en el formulario
        array(),                                                         // Campos relacionados
        array(),                                                          // Campos a ocultar del maestro en el detalle
        array('D','E','B','N')
        );
include_once('html_inf.php');
?>