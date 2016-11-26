<?php
if (!isset($_POST['nuevaSimulacion'])) {
    die(header("Location: clases.php"));
}

include 'Classclase.php';
$clase = new Classclase();
$clase->nuevaSimulacion();
$id = $clase->getClaseId();

include 'cabecera.php';
include 'lib/variables.php';
?>
<link href="css/jquery.fancybox.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-buttons.css?v=1.0.5" />
<div class="row" style="background: #111111">
    <div class="col-md-2">
        <div id="boton-iniciar" style="margin-top: 11%">
            <button id="iniciar" class="btn btn-primary" style=" width: 100%;height: 53px;">
                Iniciar simulacion
            </button>
        </div>
        <div id="boton-finalizar" style="margin-top: 11%; display: none">
            <button id="finalizar" class="btn btn-danger" style=" width: 100%;height: 53px;">
                Finalizar simulacion
            </button>
        </div>
        <div id="caja_hora">
            <font id="hora">00:00:00</font>
        </div>
    </div> 

    <div class="col-md-8">
        <iframe class="row" src="<?= $nro ?>/aircraft-dir/WebPanel/" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:700px;width:100%;" SCROLLING="no"></iframe>
    </div>
    <div class="col-md-2 row">
        <iframe class="row"  src="<?= $nro ?>" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:717px;margin-left: -7%" SCROLLING="no"></iframe>
    </div>

</div>



<!--modal-->
<div class="row">
    <div class="col-md-12">
        <a id="modal" href="#modal-container-712867" role="button" class="btn" data-toggle="modal" style="display: none"></a>

        <div class="modal fade" id="modal-container-712867" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">
                            Detalle de la simulacion
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="form-detalle" action="clases.php" method="POST">
                            <div class="form-group">
                                <label for="Instructor" class="col-sm-2 control-label">Instructor:</label>
                                <div class="col-sm-8"><input id="instructor" name="instructor" type="text" class="form-control" readonly="true"></div>				
                            </div>
                            <input type="hidden" name="id" id="id"> 
                            <input type="hidden" name="function" value="setComentario">
                            <div class="form-group">
                                <label for="Alumno" class="col-sm-2 control-label">Alumno:</label>
                                <div class="col-sm-8"><input id="alumno" name="alumno" type="text" class="form-control" readonly="true" ></div>				
                            </div>
                            <div class="form-group">
                                <label for="Fecha" class="col-sm-2 control-label">Fecha:</label>
                                <div class="col-sm-8"><input id="fecha" name="fecha" type="text" class="form-control" readonly="true" ></div>				
                            </div>
                            <div class="form-group">
                                <label for="Inicio" class="col-sm-2 control-label">Inicio:</label>
                                <div class="col-sm-8"><input id="inicio" name="inicio" type="text" class="form-control" readonly="true" ></div>				
                            </div>
                            <div class="form-group">
                                <label for="Fin" class="col-sm-2 control-label">Fin:</label>
                                <div class="col-sm-8"><input id="fin" name="fin" type="text" class="form-control"  readonly="true"></div>				
                            </div>
                            <div class="form-group">
                                <label for="Duracion" class="col-sm-2 control-label">Duracion</label>
                                <div class="col-sm-8"><input id="duracion" name="duracion" type="text" class="form-control" readonly="true" ></div>				
                            </div>
                            <div class="form-group">
                                <label for="Comentario" class="col-sm-2 control-label">Comentario</label>
                                <div class="col-sm-8"><textarea id="Comentario" name="comentario" type="text" class="form-control"  autofocus></textarea></div>				
                            </div>

                            <div class="modal-footer">
                                <input type="submit" value="Guardar" class="btn btn-primary" >

                            </div>
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
<!-- fin modal-->





</div>
</div>
<?php
include 'pie.php';
echo $nro;
?>

<script src="js/lightbox.js"></script>
<script src="js/jquery.mousewheel-3.0.6.pack.js"></script>
<script src="js/jquery.fancybox.js"></script>
<script src="js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="/js/jquery.fancybox-buttons.js?v=1.0.5"></script>

<script>
    var time;
    
    $(function(){
        $("#fallas").prop("href",'acciones/fallas.php');
        $("#mapa").prop("href",'<?= $nro ?>/mapa.html#Map');
        $('#posicion').prop("href",'acciones/posicion.php');
        $('#posicion-aterrizaje').prop("href",'acciones/posicionAterrizar.php');
    })
    
    
    $('#iniciar').click(function () {
        var id =<?= $id ?>;
        $('#boton-iniciar').hide();
        $('#boton-finalizar').show();

        $.post('Classclase.php', {id: id, function: 'iniciarSimulacion'}, function () {
            actualizarReloj();
        });
    });

    $('#finalizar').click(function () {
        var id =<?= $id ?>;
        $.post('Classclase.php', {id: id, function: 'finalizarSimulacion'}, function (data) {
            clearTimeout(time);
            $('#id').val(data["data"][0].id);
            $('#instructor').val(data["data"][0].usuario_instructor_id);
            $('#alumno').val(data["data"][0].usuario_alumno_id);
            $('#fecha').val(data["data"][0].fecha);
            $('#inicio').val(data["data"][0].inicio);
            $('#fin').val(data["data"][0].fin);
            var inicio = data["data"][0].inicio;
            var fin = data["data"][0].fin;
            $('#duracion').val(calcularDuracion(inicio, fin));
            $('#modal').click();

        }, "json");
    });


    function actualizarReloj() {
        var hora = $('#hora').text();
        var res = hora.split(":");
        seg = res[2];
        min = res[1];
        hs = res[0];
        if (parseInt(seg) < 60)
        {
            seg = parseInt(seg) + 1;
            seg = dosDigitos(seg);
        } else if (parseInt(min) < 60) {
            min = parseInt(min) + 1;
            min = dosDigitos(min);
            seg = 00;
        } else if (parseInt(hs) < 12) {
            hs = parseInt(hs) + 1;
            min = 00;
        }

        $('#hora').text(hs + ":" + min + ":" + seg);
        time = setTimeout("actualizarReloj()", 1000)
    }

    function dosDigitos(numero) {
        if (parseInt(numero) < 10)
        {
            return ('0' + numero);
        } else
            return(numero);

    }

    function calcularDuracion(inicio, fin)
    {
        inicio = inicio.split(":");
        fin = fin.split(":");
        if (inicio[2] === '0' && fin !== '0') {
            inicio[2] = 60;
        }
        seg = fin[2] - inicio[2];
        min = fin[1] - inicio[1];
        hs = fin[0] - inicio[0];
        return hs + ":" + ":" + min + ":" + seg;
    }

    $('#form-detalle').submit(function () {
        window.open('generaPDF.php');
    })

</script>