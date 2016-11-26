<?php
include '../lib/variables.php';
echo $ip;
?>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/jquery-1.12.3.js"></script>
<script src="../js/bootstrap.min.js"></script>


<script type="text/javascript">
    $(function(){
        setInicioFallas();
    })
    
    
    function DefineImagen(inputa, imagena) {

        var valor = document.getElementById(inputa).value;
        var reloj = document.getElementById(inputa+"-img").src;
        var activo = document.getElementById(inputa + "-activo").value;
        var instrumentoId = document.getElementById(inputa + '_id').value;
        if (activo == '2') {
            persisteFalla(instrumentoId, 2);

            reloj = "../images/" + inputa + "_r.png";
            document.getElementById(inputa+"-img").src = reloj;
            document.getElementById(inputa).value = 'false';
            document.getElementById(inputa + "-activo").value = '1';
        } else {
            persisteFalla(instrumentoId, 1);
            reloj = "../images/" + inputa + ".png";
            document.getElementById(inputa+"-img").src = reloj;
            document.getElementById(inputa).value = 'true';
            document.getElementById(inputa + "-activo").value = '2';
        }
    }

    function persisteFalla(instrumentoId, eventoId) {
        $.post('../Classclase.php',
                {
                    instrumentoId: instrumentoId,
                    eventoId: eventoId,
                    claseId: '10',
                    function: 'generarFalla'

                }, function () {});
    }

     var setInicioFallas = function () {
        var patch = "<?= $nro ?>/json/instrumentation/";
        var intrumentos = ['airspeed-indicator', 'attitude-indicator', 'altimeter', 'nav', 'turn-indicator', 'vertical-speed-indicator', 'adf', 'nav[1]'];
        $.each(intrumentos, function (index, value) {
            $.getJSON(patch + value + "/serviceable", function (data) {
                if (data.value) {
                    reloj = "../images/" + value + ".png";
                    document.getElementById(value+"-img").src = reloj;
                    document.getElementById(value).value = 'false';
                    document.getElementById(value + "-activo").value = '2';
                } else {
                    reloj = "../images/" + value + "_r.png";
                    document.getElementById(value+"-img").src = reloj;
                    document.getElementById(value).value = 'true';
                    document.getElementById(value + "-activo").value = '1';
                }
            });
        });
    }


</script>

<style type="text/css">
    .contenido {
        font-size: 12px;
        font-family: Verdana, Arial, Helvetica, sans-serif;
    }
</style>


<body style=" background: #4CAF50;">
    <div class="container-fluid">
        <h3 class="text-center"> Generador de fallas</h3>
        <div class="row">
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <form name="velo" method="GET" action="<?php echo $ip; ?>/instrumentation/airspeed-indicator" target="resultados">
                    <input type="hidden" name="serviceable" size="10" value="false" id="airspeed-indicator">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" name="veloactivo" size="10" value="2" id="airspeed-indicator-activo">
                    <input type="hidden" id="airspeed-indicator_id" value="1">
                    <input type="image" src="../images/airspeed-indicator.png" id="airspeed-indicator-img" onclick="DefineImagen('airspeed-indicator', 'img_velocimetro');"><br />
                    <label>Velocidad</label> 
                </form>
            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/attitude-indicator" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false" id="attitude-indicator">
                    <input type="hidden" name="horiactivo" size="10" value="2" id="attitude-indicator-activo">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" id="attitude-indicator_id" value="3">
                    <INPUT type="image" src="../images/attitude-indicator.png" id="attitude-indicator-img" onclick="DefineImagen('attitude-indicator', 'img_horizonte');"><br />
                    <label>Horizonte</label>  
                </FORM>

            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/altimeter" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false" id="altimeter">
                    <input type="hidden" name="altiactivo" size="10" value="2" id="altimeter-activo">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" id="altimeter_id" value="2">
                    <INPUT type="image" src="../images/altimeter.png" id="altimeter-img" onclick="DefineImagen('altimeter', 'img_altimetro');"><br />
                    <label>Alt&iacute;metro</label>  
                </FORM>
            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/nav" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false" id="nav">
                    <input type="hidden" name="navactivo" size="10" value="2" id="nav-activo">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" id="nav_id" value="4">
                    <INPUT type="image" src="../images/nav.png" id="nav-img" onclick="DefineImagen('nav', 'img_nav');"><br />
                    <label>NAV 1</label>  
                </FORM>
            </div>
        </div>
        <div class="row">
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/turn-indicator" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false" id="turn-indicator">
                    <input type="hidden" name="ladeoactivo" size="10" value="2" id="turn-indicator-activo">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" id="turn-indicator_id" value="5">
                    <INPUT type="image" src="../images/turn-indicator.png" id="turn-indicator-img" onclick="DefineImagen('turn-indicator', 'img_ladeo');"> <br />
                    <label>Ladeo</label> 
                </FORM>
            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/vertical-speed-indicator" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false" id="vertical-speed-indicator">
                    <input type="hidden" name="submit" value="set">
                    <input type="hidden" name="velocimetro_verticalactivo" size="10" value="2" id="vertical-speed-indicator-activo">
                    <input type="hidden" id="vertical-speed-indicator_id" value="6">
                    <INPUT type="image" src="../images/vertical-speed-indicator.png" id="vertical-speed-indicator-img" onclick="DefineImagen('vertical-speed-indicator', 'img_velocimetro_vertical');"><br />
                    <label>Velocidad vertical</label>  
                </FORM> 
            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/adf" target="resultados"> 
                    <input type="hidden" name="serviceable" size="10" value="false">
                    <input type="hidden" name="submit" value="set">
                    <INPUT type="image" src="../images/adf.png" value="Ver posicion"><br />
                    <label>ADF</label>  
                </FORM>
            </div>
            <div class="instrument col-md-3 col-xs-6  col-sm-3 instrumento">
                <td width="25%" align="center">
                    <FORM method="GET" action="<?php echo $ip; ?>/instrumentation/nav[1]" target="resultados"> 
                        <input type="hidden" name="serviceable" size="10" value="false" id="nav[1]">
                        <input type="hidden" name="nav1activo" size="10" value="2" id="nav[1]-activo">
                        <input type="hidden" name="submit" value="set">
                        <input type="hidden" id="nav[1]_id" value="8">
                        <INPUT type="image" src="../images/nav[1].png" id="nav[1]-img" onclick="DefineImagen('nav[1]', 'img_nav1');"><br />
                        <label>NAV 2</label>   
                    </FORM>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 instrumento">
                <b> Generador de fallas - Clic en un instrumento para detener</b>
            </div>
        </div>

    </div>
    <iframe name="resultados" width="0" height="0" frameborder="no"></iframe>
</body>

<style>
    .instrumento{
        text-align: center;
        color: white;

    }
    .h3
</style>