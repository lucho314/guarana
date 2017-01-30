<?php
include 'cabecera.php';
$_POST['function'] = 'vacio';
include 'Classusuario.php';
$profesores = Classusuarios::listaUsuarioInstructor();
$alumno = Classusuarios::listaUsuarioAlumno();
?>

<div id="cuadro2" class="col-sm-6 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3 ocultar">
    <form class="form-horizontal" action="panel.php" method="POST">
        <div class="form-group">
            <h3 class="col-sm-offset-2 col-sm-8 text-center">					
                Formulario nueva simulacion</h3>
        </div>
        <div class="form-group">
            <label for="profesor" class="col-sm-2 control-label">Instructor:</label>
            <div class="col-sm-8">
                <select name="profesorId" id="profesorId" class="form-control">
                    <?php
                    foreach ($profesores as $value) {
                        echo "<option value=" . $value['id'] . ">" . $value['dato'] . "</option>";
                    }
                    ?>
                </select>

            </div>
        </div>
        <div class="form-group">
            <label for="profesor" class="col-sm-2 control-label">Piloto:</label>
            <div class="col-sm-8">
                <select name="alumnoId" id="alumnoId" class="form-control">
                    <?php
                    foreach ($alumno as $value) {
                        echo "<option value=" . $value['id'] . ">" . $value['dato'] . "</option>";
                    }
                    ?>
                </select>

            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8">
                <input id="" type="submit" name="nuevaSimulacion" class="btn btn-primary" value="Guardar">
                <input id="btn_listar" type="button" class="btn btn-primary" value="Listar">
            </div>

        </div>
    </form>
</div>


<div class="row">
    <div id="cuadro1" class="col-sm-12 col-md-12 col-lg-12">
        <div class="col-sm-offset-2 col-sm-8">
            <h3 class="text-center"> <small class="mensaje"></small></h3>
        </div>
        <div class="table-responsive col-sm-12">		
            <table id="dt_clases" class="table table-striped" cellpadding="2" cellspacing="0" border="0">
                <thead>
                    <tr>								
                        <th>Piloto</th>
                        <th>Instructor</th>
                        <th>Fecha</th>
                        <th>Hora inicio</th>
                        <th>Hora Fin</th>
                        <th></th>

                    </tr>
                </thead>					
            </table>
        </div>			
    </div>		
</div>
<div>
    <form id="frmEliminarClase" action="" method="POST">
        <input type="hidden" id="idClase" name="idClase" value="">
        <input type="hidden" id="opcion" name="function" value="eliminar">
        <!-- Modal -->
        <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalEliminarLabel">Eliminar Usuario</h4>
                    </div>
                    <div class="modal-body">							
                        ¿Está seguro de eliminar la clase?<strong data-name=""></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="eliminar-clase" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
    </form>
</div>



<?php include 'pie.php'; ?>

<script>
    $(function () {
        lista();
    });

    $('#btn_listar').click(function () {
        lista();
    });

    var lista = function () {

        $('#cuadro1').slideDown("slow");
        $('#cuadro2').slideUp("slow");
        var table = $('#dt_clases').DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "Classclase.php",
                "data": {
                    'function': 'getTodos'
                }
            },
            "columns": [
                {"data": "piloto"},
                {"data": "instructor"},
                {"data": "fecha"},
                {"data": "inicio"},
                {"data": "fin"},
                {"defaultContent": "<button type='button' class='info btn btn-primary' title='Mas informacion'><i class='glyphicon glyphicon-info-sign'></i></button>	<button type='button' class='pdf btn btn-default' title='Ver PDF'><i class='fa fa-file-pdf-o'> </i></button>"}
            ],
            "dom": "Bfrtip",
            "buttons": [{
                    "text": "<i class='glyphicon glyphicon-plane'></i>+",
                    "titleAttr": "Nueva Simulacion",
                    "action": function () {
                        nueva_clase();
                    }
                }]
        });
        mas_informacion("#dt_clases tbody", table);
       pdf("#dt_clases tbody", table)

    };

    function  nueva_clase() {

        $('#cuadro1').slideUp("slow");
        $('#cuadro2').slideDown("slow");
    }

    $('#btn_listar').click(function () {
        $('#cuadro2').slideUp("slow");
        $('#cuadro1').slideDown("slow");
    });

    var obtener_id_eliminar = function (tbody, table) {
        $(tbody).on('click', 'button.eliminar', function () {
            var data = table.row($(this).parents("tr")).data();
            usuarioId = $("#frmEliminarClase #idClase").val(data.id);
        })
    }


    var mas_informacion = function (tbody, table) {

        $(tbody).on('click', 'button.info', function () {
            var data = table.row($(this).parents("tr")).data();
            var id = data.id;
            window.location.href = 'detalleClase.php?id=' + id;
        })
    }

    var pdf=function (tbody, table){

        $(tbody).on('click', 'button.pdf', function () {
            var data = table.row($(this).parents("tr")).data();
            var id = data.id;
            window.location.href = "detallePdf.php?claseId="+ id;
        })
    }





    $("#eliminar-clase").click(function () {
        var idClase = $('#idClase').val();
        $.ajax({
            method: "POST",
            url: "Classclase.php",
            data: {"idClase": idClase, "function": 'eliminar'}
        }).done(function (info) {
            var json_info = JSON.parse(info);
            mostrar_mensaje(json_info);
            lista();
        });
    });

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