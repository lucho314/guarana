<?php
include 'cabecera.php';
include 'Classclase.php';
$claseId = $_GET['id'];
$clase = new Classclase();
$detalleClase = $clase->getClase($claseId);
$fallas = $clase->getFallas($claseId);
?>

<div class="row">
    <div class="col-md-6">
        <div id="datos">
            <h4 class="text-center">Datos de la simulacion:</h4>
            <table class="table-bordered" style="width: 100%; background: white">
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
                        <b>Profesor:</b>
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
            <table class="table-bordered" style="width: 100%; background: white; margin-top: 2%">
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
    <div class="col-md-6">
        <h4 class="text-center">Flight History:</h4>
        <img src="Flight_History/mapa.png">
    </div>
</div>

<div class="row">
    <div class="col-md-12">
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    </div>
</div>


<?php include 'pie.php'; ?>


<style>
    td{
        height: 36px;

    }
</style>
