<?php
include '../lib/variables.php';

$claseId = $_GET['claseId'];
?>
<head>
    <title>Puesto</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-1.12.3.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</head>
<h2 class="text-center"> Captura y carga de Flight History</h2>
<div class="col-md-8" id="panel">
    <iframe class="row" src="<?= $nro ?>/mapa.html#Map" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:480px;width:80%;" SCROLLING="no"></iframe>
</div>
<b>Si realizo la captura busque y seleccione el archivo "simulacion_<?= $claseId ?>.jpg", de lo contrario realize la captura como se indica <a href="../manuales/captura_Flight_History.pdf" target="_blank"> aqui</a> </b>
<div>
    <form enctype="multipart/form-data" action="../Classclase.php" method="POST">
        <div class="col-xs-6"> <input name="uploadedfile" type="file" class="form-control" required="true"/></div>
        <input type="hidden" name="claseId" value="<?= $claseId ?>">
        <input type="hidden" value="flightHistory" name="function">
        <input type="submit" value="Subir archivo" />
    </form>
</div>

<script>
    $('form').submit(function () {


        parent.jQuery.fancybox.close();
        parent.finalizarSimulacion();

    });
</script>