<?php

include_once('lib/connect_mysql.php');
include_once('lib/funciones.php');
$idSalon=$_POST['id'];
$funcion= $_POST['funcion'];
echo $funcion($idSalon); 

function getPrecio($idSalon)
{
    return DevuelveValor($idSalon, 'importe', 'salons','id');
}