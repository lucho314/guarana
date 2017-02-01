<?php
include_once '../lib/connect_mysql.php';
header('Content-type: text/json');
// TOMO LOS DATOS DE LA BASE DE DATOS
 $sql = "SELECT reservas.id,fecha_reserva,afiliados.descripcion as 'afiliado',salons.descripcion as 'salon',turnos.descripcion as 'turno' FROM reservas "
        . "INNER JOIN afiliados on afiliados.id=reservas.afiliado_id "
        . "INNER JOIN salons on salons.id=reservas.salon_id "
        . "INNER JOIN turnos on turnos.id=reservas.turno_id";
$result = mysql_query($sql);
echo '[';
$separator = "";

while ($r = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo $separator;
    $fecha      = $r['fecha_reserva'];
    $hora       = $r['hora'];
    $id         = $r['id'];
    $fechaYhora = $fecha.' '.$r['turno'];
    $descripcion=$r['salon']." - ".$r['turno']." - ".$r['afiliados'];
    echo '  { "date": "'.$fechaYhora.'", "type": "meeting", "title": "Recerva", "description": "'.$descripcion.'", "url": "detalle_agenda.php?id='.$id.'" }';
 $separator = ",";   
}
echo ']';
?>