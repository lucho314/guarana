<?php

include_once 'lib/connect_mysql.php';



$dni = 12385671;
for ($i = 0; $i < 90; $i++) {

    $sql = "INSERT INTO `persona` (`dni`, `nombre`, `apellido`, `domicilio`, `telefono`, `email`) VALUES ('$dni', 'asd', 'asd', 'asd', '456', NULL);";
    echo mysql_query($sql);
    echo $sql = "INSERT INTO `deportista` (`dni`, `id_planilla`, `numero_socio`, `fecha_nac`) VALUES ('$dni', '1', '123', '2017-01-11');";
    $dni++;
    echo mysql_query($sql);
}