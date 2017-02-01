<?php
$tip = '';
include_once('html_sup.php');
include("scaffold.php");

new Scaffold("editable","familiars",
        30,
        array('afiliado_id','parentesco_id','nombre','apellido'),
        array(),               // Campos a ocultar en el formulario
        array(),                                                         // Campos relacionados
        array(),                                                          // Campos a ocultar del maestro en el detalle
        array('D','E','B','N')
        );
include_once('html_inf.php');
?>