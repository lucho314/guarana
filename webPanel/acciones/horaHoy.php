<?php
include '../lib/variables.php';
$json = file_get_contents($nro . "/json/sim/time/gmt");
$obj = json_decode($json);
$arrayGtm = explode("T", $obj->value);
$gmt = $arrayGtm[0];
$claseId = $_GET['claseId'];
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/bootstrap.min.css">

        <link rel="stylesheet" href=" http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="../css/jquery-ui-timepicker-addon.min.css">
        <script src="../js/jquery-1.12.3.js"></script>
        <script src="http://code.jquery.com/ui/1.11.0/jquery-ui.min.js"></script>
        <script src="../js/jquery-ui-sliderAccess.js"></script>
        <script src="../js/jquery-ui-timepicker-addon.min.js"></script>
    </head>
    <body>


        <div class="container-fluid">
            <h2 class="text-center"> Hora de hoy</h2>

        </fieldset>


        <div class="col-xs-12">
            <div class="col-xs-12">
                <h3 class="text-center"> <small class="mensaje"></small></h3>
            </div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Automatico</legend>
                <div class="control-group">
                    <div class="col-xs-2">
                        <form method="GET" action="<?php echo $ip; ?>sim/time" class="set-automatico" target="resultados">
                            <input type="hidden" name="gmt" id="gmt"  value="<?= $gmt ?>T10:00:00">
                            <input type="hidden" name="gmt" id="real"  value="<?= $gmt ?>T06:00:00">
                            <button type="submit" value="set" name="submit" class="bt btn-primary col-xs-12 form-control bt btn-primary">Mañana</button>
                        </form>
                    </div>
                    <div class="col-xs-2">
                        <form method="GET" action="<?php echo $ip; ?>sim/time" class="set-automatico" target="resultados">
                            <input type="hidden" name="gmt" id="gmt"  value="<?= $gmt ?>T16:00:00">
                            <input type="hidden" name="gmt" id="real"  value="<?= $gmt ?>T12:00:00">
                            <button type="submit"  value="set" name="submit" class="bt btn-primary col-xs-12 form-control bt btn-primary">Mediodia</button>
                        </form>
                    </div>
                    <div class="col-xs-2">
                        <form method="GET" action="<?php echo $ip; ?>sim/time" class="set-automatico" target="resultados">
                            <input type="hidden" name="gmt" id="gmt"  value="<?= $gmt ?>T21:00:00">
                            <input type="hidden" name="gmt" id="real"  value="<?= $gmt ?>T17:00:00">
                            <button type="submit" value="set" name="submit" class="bt btn-primary col-xs-12 form-control bt btn-primary">Tarde</button>
                        </form>
                    </div>
                    <div class="col-xs-2">
                        <form method="GET" action="<?php echo $ip; ?>sim/time" class="set-automatico" target="resultados">
                            <input type="hidden" name="gmt" id="gmt"  value="<?= $gmt ?>T23:59:00">
                            <input type="hidden" name="gmt" id="real"  value="<?= $gmt ?>T20:00:00">
                            <button type="submit"  value="set" name="submit" class="bt btn-primary col-xs-12 form-control bt btn-primary">Anochecer</button>
                        </form>
                    </div>
                    <div class="col-xs-2">
                        <form method="GET" action="<?php echo $ip; ?>sim/time" class="set-automatico" target="resultados">
                            <input type="hidden" name="gmt" id="gmt"  value="<?= $gmt ?>T02:00:00">
                            <input type="hidden" name="gmt" id="real"  value="<?= $gmt ?>T22:00:00">
                            <button type="submit"  value="set" name="submit" class="bt btn-primary col-xs-12 form-control bt btn-primary">Noche</button>
                        </form>
                    </div>

                </div>
        </div>

        <div class="col-xs-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Manual</legend>

                <div class="form-group">
                    <label for="nombres" class="col-sm-2 control-label">Nueva Hora:</label>
                    <div class="col-xs-6"><input id="nuevaHora" name="nombres" type="text" class="form-control" placeholder="hs:mm"></div>


                    <form method="GET" action="<?php echo $ip; ?>sim/time" id="set-manual" target="resultados">
                        <input type="hidden" name="gmt" id="gmt">
                        <div class="col-xs-2"><button type="submit" id="aceptar" value="set" name="submit" class="form-control bt btn-primary">Aplicar</button></div>
                    </form>
                </div>
            </fieldset>
        </div>

    </div>
    <iframe name="resultados" width="0" height="0" frameborder="no"></iframe>
</body>


<style>
    fieldset.scheduler-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
        box-shadow:  0px 0px 0px 0px #000;
    }

    legend.scheduler-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:inherit; /* Or auto */
        padding:0 10px; /* To give a bit of padding on the left and right */
        border-bottom:none;

    }
</style>

<script>

    $('#nuevaHora').datetimepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('#aceptar').click(function (event) {
        event.preventDefault();
        var gmtPiker = $('#nuevaHora').datepicker("getDate");
        persistenciaCambioHora($('#nuevaHora').val(), 2);
        $('#gmt').val(transformGmtSimu(gmtPiker));
        alert($('#gmt').val());
    });

    $('.set-automatico').submit(function(event){
       var hora=$(this).children('#real').val();
       hora=hora.replace('T',' ');
       persistenciaCambioHora(hora, 1);
    });
    /*
     * 
     * @param {type} gmtPiker
     * @returns {String}
     * recibe un gmt generado por picker de jqueryui
     * devuelve un gmt aumentado en 3 hs en el formato aceptado por el simulador
     * aaaa-mm-ddThs:min:seg
     */
    function  transformGmtSimu(gmtPiker) {
        //new Date(year, month, day, hours, minutes);
        var hora = new Date(gmtPiker);
        hora.setHours(hora.getHours() + 4);
        var anio = hora.getFullYear();
        var mes = menorDiez(hora.getMonth() + 1);
        var dia = menorDiez(hora.getDay());
        var hs = menorDiez(hora.getHours());
        var min = menorDiez(hora.getMinutes());
        var seg = "00";
        var gmt = anio + "-" + mes + "-" + dia + "T" + hs + ":" + min + ":" + seg;
        return gmt
    }
    

    /*
     * @param {type} numero
     * @returns {String}
     *si el numero es menor a 10 le agrega le antepone un 0 
     * de lo contrario retorna numero sin modificarlo.*/
    function menorDiez(numero) {
        if (numero < 10)
        {
            return '0' + numero;
        }
        return numero;

    }

    /**
     * @param {type} gmt formato dd/mm/aaaa hs:ms:ss
     * @param {int} tipoCambio (1: Automatico; 2: Manual)
     * Envia una solicitud ajax para que se guarden los cambios en la base de datos
     * */
    function persistenciaCambioHora(gmt, tipoCambio) {
        $.post('../Classclase.php', {function: 'cambioHora', gmt: gmt, claseId:<?= $claseId ?>, tipoCambio: tipoCambio}, function (info) {
            var json_info = JSON.parse(info);
            mostrar_mensaje(json_info);
        })

    }



    var mostrar_mensaje = function (informacion) {
        var texto = "", color = "";
        if (informacion.respuesta == "BIEN") {
            texto = "<strong>Bien!</strong> Se han guardado los cambios correctamente.";
            color = "#379911";
        } else if (informacion.respuesta == "ERROR") {
            texto = "<strong>Error</strong>, no se ejecutó la consulta.";
            color = "#C9302C";
        } else if (informacion.respuesta == "EXISTE") {
            texto = "<strong>Información!</strong> el usuario ya existe.";
            color = "#5b94c5";
        } else if (informacion.respuesta == "VACIO") {
            texto = "<strong>Advertencia!</strong> debe llenar todos los campos solicitados.";
            color = "#ddb11d";
        }

        $(".mensaje").html(texto).css({"color": color});
        $(".mensaje").fadeOut(5000, function () {
            $(this).html("");
            $(this).fadeIn(3000);
        });
    }
</script>
</html>
