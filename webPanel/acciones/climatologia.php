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
            <p class="text-center" style="font-size: 19px"> Ejemplo: 2014/07/17 12:30 SAAP 012345Z 27050G23KT 280V220 1500 RA SCT024 OVC004 13/13 Q0800</p>
            <font size="2">
            Fecha y hora del parte, aerodromo, dia y hora, viento a 270 grados 50 nudos y rafagas de 24 nudos variables entre 280 y 220 grados, visibilidad 1500 pies, lluvia, nubes dispersas a 2400 pies, cubierto a 400 pies, temperatura 13 grados y punto de rocio a 13 grados, presion atmosferica 800 hectopascales.
            <br><br>
            <p class="text-center"><b >Por defecto es XXXX 01234<5Z 15003KT 12SM SCT041 FEW200 20/08 Q1015 NOSIG</b></p>
            <br>
            <form class="col-xs-offset-2 col-xs-8" method="GET" action="<?php echo $ip; ?>/environment/metar" target="resultados">
                <B>METAR</B> = 
                <textarea name="data" rows="3" cols="30" class="form-control"></textarea>
                <button type="submit" class="bt btn-primary" value="set" name="submit">Aplicar</button>
            </FORM>
        </div>
        <div class="col-xs-12" id="setPerfil" style="display:none">
            <h2 class="text-center">Perfiles Climatol&oacute;gicos</h2>

            <p style="font-size: 19px"> Los perfiles climatol&oacute;gicos permiten seleccionar condiciones espec&iacute;ficas relacionadas con el entrenamiento de pilotos en condiciones de baja visibilidad.</p>
            <form method="GET" action="<?php echo $ip; ?>/environment" target="resultados">
                <div class="col-xs-6 col-xs-offset-3">
                    <select class="form-control" name="weather-scenario">
                        <option value="Early morning fog">Niebla matutina</option>
                        <option value="CAT I minimum">M&iacute;nimo categor&iacute;a I</option>
                        <option value="CAT II minimum">M&iacute;nimo categor&iacute;a II</option>
                        <option value="CAT IIIb minimum">M&iacute;nimo categor&iacute;a IIIb</option>
                        <option value="Marginal VFR">VFR marginal</option>
                        <option value="Stormy Monday">Lunes tormentoso</option>
                        <option value="Thunderstorm">Tormenta</option>
                        <option value="Core low pressure region">Cicl&oacute;n (centro baja presi&oacute;n)</option>
                        <option value="Low pressure region">Regi&oacute;n de baja presi&oacute;n</option>
                        <option value="Border of a low pressure region">Borde de regi&oacute;n de baja presi&oacute;n</option>
                        <option value="Border of a high pressure region">Borde de regi&oacute;n de alta presi&oacute;n</option>
                        <option value="High pressure region">Regi&oacute;n de alta presi&oacute;n</option>
                        <option value="Core high pressure region">Anticicl&oacute;n ( centro alta presi&oacute;n)</option>
                    </select>

                </div >
                <div class="col-xs-3 col-xs-offset-3" style="margin-top: 7px;">
                    <button type="submit" value="set" name="submit" class="form-control bt btn-primary">Aplicar</button>
                </div>


            </form>

        </div>

    </div>
     <iframe name="resultados" width="0" height="0" frameborder="no"></iframe>
</body>

<style>
    #botonera{
        margin-top: 1%;
    }
</style>

<script>
    $('#perfil').click(function () {
        $('#setManual').hide();
        $('#manual').removeClass("btn-primary");
        $('#manual').addClass("btn-success");
        $('#perfil').removeClass("btn-success");
        $('#perfil').addClass("btn-primary");
        $('#setPerfil').show();
    });
    $('#manual').click(function () {
        $('#setManual').show();
        $('#manual').removeClass("btn-success");
        $('#manual').addClass("btn-primary");
        $('#perfil').removeClass("btn-primary");
        $('#perfil').addClass("btn-success");
        $('#setPerfil').hide();
    });
</script>