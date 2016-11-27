<?php
include '../lib/variables.php';
?>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/jquery-1.12.3.js"></script>
<script src="../js/bootstrap.min.js"></script>
<body style="background: #C4CC66">
    <div class="container-fluid">
        <div class="col-xs-12" id="botonera">
            <button class="bt btn-primary col-xs-2" id="manual">Manual</button>
            <button class="bt btn-success col-xs-2" id="perfil">Perfiles</button>
            <button class="bt btn-success col-xs-2" id="verMetar">Ver METAR</button>
        </div>
        <div class="col-xs-12" id="setManual">
            <h2 class="text-center">Climatología (METAR)</h2>
            <p class="text-center"> Ejemplo: 2014/07/17 12:30 SAAP 012345Z 27050G23KT 280V220 1500 RA SCT024 OVC004 13/13 Q0800</p>
            <font size="2">
            Fecha y hora del parte, aerodromo, dia y hora, viento a 270 grados 50 nudos y rafagas de 24 nudos variables entre 280 y 220 grados, visibilidad 1500 pies, lluvia, nubes dispersas a 2400 pies, cubierto a 400 pies, temperatura 13 grados y punto de rocio a 13 grados, presion atmosferica 800 hectopascales.
            <br><br>
            <p class="text-center"><b >Por defecto es XXXX 01234<5Z 15003KT 12SM SCT041 FEW200 20/08 Q1015 NOSIG</b></p>
            <br>
            <form class="col-xs-offset-2 col-xs-8" method="GET" action="<?php echo $ip; ?>/environment/metar" target="resultados">
                <B>METAR</B> = 
                <textarea name="data" rows="3" cols="30" class="form-control"></textarea>
                <button type="submit" value="set" name="submit">Aplicar</button>
            </FORM>
        </div>
        <div class="col-xs-12" id="setPerfil" style="display:none">
            <h2 class="text-center">Perfiles Climatol&oacute;gicos</h2>
            <font size="2">
            Los perfiles climatol&oacute;gicos permiten seleccionar condiciones espec&iacute;ficas relacionadas con el entrenamiento de pilotos en condiciones de baja visibilidad.


        </div>

    </div>
</body>

<style>
    #botonera{
        margin-top: 1%;
    }
</style>

<script>
    $('#perfil').click(function () {
        $('#setManual').hide();
        $('#setPerfil').show();
    });
</script>