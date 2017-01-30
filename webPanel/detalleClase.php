<?php
include 'cabecera.php';
include 'Classclase.php';
$claseId = $_GET['id'];
$clase = new Classclase();
$detalleClase = $clase->getClase($claseId);
$fallas = $clase->getFallas($claseId);
$horas = $clase->getHoras($claseId);
$climas = $clase->getClimas($claseId);
?>

<div class="row">
    <div class="col-md-6">
        <div id="datos">
            <h4 class="text-center">Datos de la simulacion:</h4>
            <table class="table-bordered" border="1px" style="width: 100%; background: white">
                <tr>
                    <td width="40%">
                        <b>Piloto:</b>
                    </td>
                    <td>
                        <?= $detalleClase["data"][0]["usuario_alumno_id"] ?> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Instructor:</b>
                    </td>
                    <td>
                        <?= $detalleClase["data"][0]["usuario_instructor_id"] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b> Fecha:</b>
                    </td>
                    <td>
                        <?= $detalleClase["data"][0]["fecha"] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Hora de inicio:</b>
                    </td>
                    <td>
                        <?= $detalleClase["data"][0]["inicio"] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Hora de Fin:</b>
                    </td>
                    <td>
                        <?= $detalleClase["data"][0]["fin"] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b> Duracion simulacion:</b>

                    </td>
                    <td>
                        <?php
                        $diferencia = explode(".", $detalleClase["data"][0]["diferencia"]);
                        echo $diferencia[0];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>  comentario:</b>
                    </td>
                    <td>
<?= $detalleClase["data"][0]["comentario"] ?> 
                    </td>
                </tr>
            </table>
        </div>
        <div id="fallas" style=" margin-top: 2%">
            <h4 class="text-center">Fallas:</h4>
            <div class="detalle">
                <table class="table-bordered "  border="1px"  style="width: 100%; background: white; margin-top: 2%">
                    <tr>
                        <th> Instrumento</th>
                        <th>Evento</th>
                        <th> Hora</th>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($fallas['data']); $i++):
                        ?>
                        <tr>
                            <td><?= $fallas['data'][$i]['descripcion'] ?></td>
                            <td><?= $fallas['data'][$i]['evento'] ?></td>
                            <td><?= $fallas['data'][$i]['hora'] ?></td>
                        </tr>
<?php endfor; ?>
                </table>
            </div>
        </div>
        <div id="fallas" style=" margin-top: 2%">
            <h4 class="text-center">Cambio "Hora de hoy":</h4>
            <div class="detalle">
                <table class="table-bordered"  border="1px"  style="width: 100%; background: white; margin-top: 2%">
                    <tr>
                        <th> Fecha</th>
                        <th>Hora</th>
                        <th> Forma de cambio</th>
                    </tr>
                    <?php
                    for ($i = 0; $i < count($horas['data']); $i++):
                        ?>
                        <tr>
                            <td><?= $horas['data'][$i]['fecha'] ?></td>
                            <td><?= $horas['data'][$i]['hora'] ?></td>
                            <td><?= mb_convert_encoding($horas['data'][$i]['forma_cambio'], "UTF-8", "ISO-8859-1"); ?></td>
                        </tr>
<?php endfor; ?>
                </table>
            </div>
        </div>

    </div>
    <div class="col-md-6" style="
         margin-left: -16px;
         ">
        <h4 class="text-center">Flight History:</h4>
        <?php if(file_exists("Flight_History/simulacion_".$claseId.".jpg")):?>
        <img src="Flight_History/simulacion_<?= $claseId ?>.jpg">
    <?php else:?>
        <h2>No existe captura de mapa para esta simulación</h2>
    <?php endif;?>
    </div>
</div>

<div class="row">
    <div class="col-md-12" style="margin-top: 2%" class="detalle">
        <h4 class="text-center">Cambio "Clima":</h4>
        <div class="detalle">
            <table class="table-bordered"  border="1px"  style="width: 100%; background: white;">
                <tr>
                    <th> Hora</th>
                    <th>Metar</th>
                    <th> Forma de cambio</th>
                </tr>
                <?php
                for ($i = 0; $i < count($climas['data']); $i++):
                    ?>
                    <tr>
                        <td><?= $climas['data'][$i]['hora'] ?></td>
                        <td><?= mb_convert_encoding($climas['data'][$i]['metar'], "UTF-8", "ISO-8859-1"); ?></td>
                        <td><?= mb_convert_encoding($climas['data'][$i]['forma_cambio'], "UTF-8", "ISO-8859-1"); ?></td>
                    </tr>
<?php endfor; ?>
            </table>
        </div>
    </div>
</div>


<?php include 'pie.php'; ?>


<style>
    td{
        height: 36px;

    }

    .detalle {
        max-height: 160px;
        overflow-y: scroll;
    }
</style>
