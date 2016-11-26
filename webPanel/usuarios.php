<?php
include 'cabecera.php';
$_POST['function']='vacio';
include 'Classusuario.php';
$tipo=$_GET['tipo'];
$profesores=  Classusuarios::listaUsuarioInstructor();
?>

<div class="row">
    <div id="cuadro2" class="col-sm-12 col-md-12 col-lg-12 ocultar">
        <form class="form-horizontal" action="" method="POST">
            <div class="form-group">
                <h3 class="col-sm-offset-2 col-sm-8 text-center">					
                    Formulario de Registro</h3>
            </div>
            <input type="hidden" id="usuarioId" name="usuarioId" value="0">
            <input type="hidden" id="function" name="function" value="nuevo">
            <input type="hidden" id="tipo" name="tipo" value="<?= $tipo?>">
            <div class="form-group">
                <label for="nombres" class="col-sm-2 control-label">Nombres</label>
                <div class="col-sm-8"><input id="nombres" name="nombres" type="text" class="form-control"  autofocus></div>				
            </div>
            <div class="form-group">
                <label for="apellidos" class="col-sm-2 control-label">Apellidos</label>
                <div class="col-sm-8"><input id="apellidos" name="apellidos" type="text" class="form-control" ></div>
            </div>
            <div class="form-group">
                <label for="dni" class="col-sm-2 control-label">Dni</label>
                <div class="col-sm-8"><input id="dni" name="dni" type="text" class="form-control" maxlength="8" ></div>
            </div>
            <div class="form-group">
                <label for="usuario" class="col-sm-2 control-label">Usuario</label>
                <div class="col-sm-8"><input id="usuario" name="usuario" type="text" class="form-control" maxlength="8" ></div>
            </div>
             <div class="form-group <?= ($tipo==='1')?'ocultar':'' ?>">
                <label for="profesor" class="col-sm-2 control-label">Instructor a cargo</label>
                <div class="col-sm-8">
                    <select name="profesor" id="profesor" class="form-control">
                        <?php foreach ($profesores as $value) {
                            echo "<option value=".$value['id'].">".$value['dato']."</option>";
                        }?>
                    </select>
                    
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-8"><input id="password" name="password" type="password" class="form-control" maxlength="8" ></div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8">
                    <input id="" type="submit" class="btn btn-primary" value="Guardar">
                    <input id="btn_listar" type="button" class="btn btn-primary" value="Listar">
                </div>
            </div>
        </form>
        <div class="col-sm-offset-2 col-sm-8">
            <p class="mensaje"></p>
        </div>

    </div>
</div>
<div class="row">
    <div id="cuadro1" class="col-sm-12 col-md-12 col-lg-12">
        <div class="col-sm-offset-2 col-sm-8">
            <h3 class="text-center"> <small class="mensaje"></small></h3>
        </div>
        <div class="table-responsive col-sm-12">		
            <table id="dt_proveedor" class="table table-striped" cellpadding="2" cellspacing="0" border="0">
                <thead>
                    <tr>								
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>DNI</th>
                        <th>Usuario</th>
                        <th></th>

                    </tr>
                </thead>					
            </table>
        </div>			
    </div>		
</div>
<div>
    <form id="frmEliminarUsuario" action="" method="POST">
        <input type="hidden" id="idusuario" name="idusuario" value="">
        <input type="hidden" id="opcion" name="opcion" value="eliminar">
        <!-- Modal -->
        <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalEliminarLabel">Eliminar Usuario</h4>
                    </div>
                    <div class="modal-body">							
                        ¿Está seguro de eliminar al usuario?<strong data-name=""></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="eliminar-usuario" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
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
        var table = $('#dt_proveedor').DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "Classusuario.php?tipo=<?= $tipo?>"
            },
            "columns": [
                {"data": "nombre"},
                {"data": "apellido"},
                {"data": "dni"},
                {"data": "usuario"},
                {"defaultContent": "<button type='button' class='editar btn btn-primary' title='Editar'><i class='fa fa-pencil-square-o'></i></button>	<button type='button' class='eliminar btn btn-danger' data-toggle='modal' data-target='#modalEliminar' title='Eliminar'><i class='fa fa-trash-o'></i></button>"}
            ],
            "dom": "Bfrtip",
            "buttons": [{
                    "text": "<i class='fa fa-user-plus'></i>",
                    "titleAttr": "Agregar Usuario",
                    "action": function () {
                        agregar_nuevo_usuario();
                    }
                }]
        });
        obtener_data_editar("#dt_proveedor tbody", table);
        obtener_id_eliminar("#dt_proveedor tbody", table);
    };

    agregar_nuevo_usuario = function () {
        limpiar_datos();
        $('#cuadro2').slideDown("slow");
        $('#cuadro1').slideUp("slow");

    }
    var obtener_data_editar = function (tbody, table) {

        $(tbody).on('click', 'button.editar', function () {
            var data = table.row($(this).parents("tr")).data();
            $('#nombres').val(data.nombre);
            $('#apellidos').val(data.apellido);
            $('#dni').val(data.dni);
            $('#usuario').val(data.usuario);
            $('#password').val(data.password);
            $('#usuarioId').val(data.id);
            $('#function').val('editar');
           <?= ($tipo==='2')?'get_profesor(data.id);':'' ?>
            $('#cuadro2').slideDown("slow");
            $('#cuadro1').slideUp("slow");

        })
    }
    var obtener_id_eliminar = function (tbody, table) {
        $(tbody).on('click', 'button.eliminar', function () {
            var data = table.row($(this).parents("tr")).data();
            usuarioId = $("#frmEliminarUsuario #idusuario").val(data.id);
        })
    }

    var guardar = $("form").submit(function (e) {
        e.preventDefault();
        var frm = $(this).serialize();
        //console.log(frm);
        $.ajax({
            method: "POST",
            url: "Classusuario.php",
            data: frm
        }).done(function (info) {
            var json_info = JSON.parse(info);
            mostrar_mensaje(json_info);
            //console.log(json_info);
            limpiar_datos()
            lista();

        })
    })

        $("#eliminar-usuario").click(function () {
            var idusuario = $('#idusuario').val();
            $.ajax({
                method: "POST",
                url: "Classusuario.php",
                data: {"idusuario": idusuario, "function": 'eliminar'}
            }).done(function (info) {
                var json_info = JSON.parse(info);
                mostrar_mensaje(json_info);
                limpiar_datos()
                lista();
            })
        })

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

    var limpiar_datos = function () {
        $('#nombres').val("");
        $('#apellidos').val("");
        $('#dni').val("");
        $('#usuario').val("");
        $('#password').val("");
        $('#function').val('nuevo');
    }

function get_profesor(id){
    $.post('Classusuario.php',{id:id,function:'idInstructorAlumno'},function(data){
           $('#profesor').val(JSON.parse(data));
        }, "html")
}

</script>