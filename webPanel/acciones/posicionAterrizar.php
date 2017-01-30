<?php
include '../lib/variables.php';
$jsonInfo = file_get_contents("$nro/json/sim/airport/closest-airport-id");
$airportClass = json_decode($jsonInfo);
$letra[0] = substr($airportClass->value, 0, 1);
$letra[1] = substr($airportClass->value, 1, 1);
$letra[2] = substr($airportClass->value, 2, 1);
$ruta = "../Airports/" . $letra[0] . "/" . $letra[1] . "/" . $letra[2] . "/" . $airportClass->value . ".threshold.xml";
$xml = simplexml_load_file($ruta);
$runway[0] = $xml->runway[0]->threshold[0]->rwy;
$runway[1] = $xml->runway[0]->threshold[1]->rwy;
?>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/jquery-1.12.3.js"></script>
<script src="../js/bootstrap.min.js"></script>


<body style=" background: #4CAF50;">
        <form class="form-horizontal" id="formulario" action="panel.php" method="POST">
            <div class="form-group">
                <h3 class="col-sm-offset-2 col-sm-9 text-center">					
                    Posicion de aterrizaje</h3>
            </div>
            <div class="form-group">
                <label for="airport" class="col-sm-3 control-label">Airport:</label>
                <div class="col-sm-7">
                    <input type="text" id="airport" value="<?= $airportClass->value ?>" readonly="true" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="profesor" class="col-sm-3 control-label">Distancia (mn):</label>
                <div class="col-sm-7">
                    <input type="number" id="distancia" value="3" readonly="true" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="profesor" class="col-sm-3 control-label">Altitude (mn):</label>
                <div class="col-sm-7">
                    <input type="number" id="altitude" value="1500" readonly="true" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="profesor" class="col-sm-3 control-label">Airspeed (mn):</label>
                <div class="col-sm-7">
                    <input type="number" id="airspeed" value="120" readonly="true" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="profesor" class="col-sm-3 control-label">Runway:</label>
                <div class="col-sm-7">
                    <select name="runway" id="runway" class="form-control">
                        <option><?= $runway[0] ?></option>
                        <option><?= $runway[1] ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-7">
                    <input id="" type="submit" name="aterrizar" class="btn btn-primary" value="Aceptar">
                </div>

            </div>
        </form>
</body>

<script>
    $("#formulario").submit(function (event) {
        event.preventDefault();
        var altura = $('#altitude').val();
        var velocidad = $('#airspeed').val();
        var runway = $("#runway").val();
        
        $.post('posicionAterrizarServidor.php',{altura:altura,velocidad:velocidad,runway:runway});
});

</script>