<?php
include_once('html_sup.php');
include("scaffold.php");
//error_reporting(-1);

new Scaffold(
        "editable",
        "agendas",
        30,
        array('fecha','colaborador_id','titulo','descripcion'),
        array('empresa_id'),               // Campos a ocultar en el formulario
        array(),                                                         // Campos relacionados
        array(),                                                          // Campos a ocultar del maestro en el detalle
        array('D','E','B','N')
        );

include_once('html_inf.php');
?>            
