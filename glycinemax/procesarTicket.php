<?php

include 'enviaMail.php';
include_once('lib/conect_mysql.php');
include_once('lib/funciones.php');
$afiliadoId = 1; //$_SESSION['usuario_id'];
$clasificacionId = $_POST['clasificacion'];
$tipoId = $_POST['tipo'];
$celularId = $_POST['celularId'];
$detalle = $_POST['detalle'];
$familiarId = $_POST['familiarId'];
$afiliado = DevuelveValor($afiliadoId, 'descripcion', 'afiliados', 'id');
$familiar = DevuelveValor($familiarId, 'descripcion', 'familiars', 'id');
$celular = DevuelveValor($celularId, 'nro_telefono', 'celulars', 'id');

echo $html = "<h2 style='text-align: center'>"
 . "Nuevo Ticket</h2>" .
 " <b>clasificacion:</b> "
 . "<br><b>Tipo:</b>"
 . " <br> <b>Afiliado:</b> " . $afiliado
 . " <br> <b>Familiar:</b> " . $familiar .
 " <br> <b>Celular: </b>" . $celular .
 " <br> <b>Detalle: </b> " . $detalle;

$subject = 'Nuevo ticket';

$email = new enviaMail();
if ($email->enviaHtml($htnl, $subject, ['luciano.zapata314@gmail.com'])) {
    $msj = "Ticket generado correctamente";
} else {
    $msj = "Ocurrio un problema al generar el ticket";
}

//header('Location:nuevoTicket.php?mensaje=' . $msj);
